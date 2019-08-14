<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChangedEvent;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingDetailRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\Step;
use App\Scopes\GetValueScope;
use App\Services\ExampleBuildingService;
use Illuminate\Http\Request;

class BuildingDetailController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace(['tool', '/'], '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    public function index(Request $request, Cooperation $cooperation)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $buildingTypes = BuildingType::all();

        return view('cooperation.tool.building-detail.index', compact('building', 'buildingTypes'));
    }

    public function store(BuildingDetailRequest $request)
    {
        /** @var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();
        $buildYear = $request->get('build_year');
        $buildingTypeId = $request->get('building_type_id');

        // to get the old building features
        $currentFeatures = $building->buildingFeatures;

        $features = BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'build_year' => $buildYear,
            ]
        );

        $buildingType = BuildingType::find($buildingTypeId);
        $features->buildingType()->associate($buildingType);
        $features->save();
        $exampleBuilding = $this->getGenericExampleBuildingByBuildingType($buildingType);

        // if there are no features yet, then we can apply the example building
        // else, we need to compare the old buildingtype and buildyear against that from the request, if those differ then we apply the example building again.
        /** @note: this is a feature that was requested, however it needs to be removed, we just keep this commented out in case it
         *  needs to be turned on again
         */
        if (! $currentFeatures instanceof BuildingFeature) {
            if ($exampleBuilding instanceof ExampleBuilding) {
                ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

                // we need to associate the example building with it after it has been applied since we will do a check in the ToolSettingTrait on the example_building_id
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        } else {
            $currentBuildYear = $currentFeatures->build_year;
            $currentBuildingTypeId = $currentFeatures->building_type_id;

            // compare the old ones vs the request
            if (($currentBuildYear != $buildYear || $currentBuildingTypeId != $buildingTypeId) && $exampleBuilding instanceof ExampleBuilding) {
                ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

                // we need to associate the example building with it after it has been applied since we will do a check in the ToolSettingTrait on the example_building_id
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        }

        // finish the step
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));

        \Event::dispatch(new StepDataHasBeenChangedEvent());

        return redirect()->route('cooperation.tool.general-data.index');
    }

    /**
     * Get a example building based on the building type.
     *
     * @param BuildingType $buildingType
     *
     * @return ExampleBuilding|\Illuminate\Database\Eloquent\Builder
     */
    private function getGenericExampleBuildingByBuildingType(BuildingType $buildingType)
    {
        $exampleBuilding = ExampleBuilding::generic()->where('building_type_id', $buildingType->id)->first();

        return $exampleBuilding;
    }
}
