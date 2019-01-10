<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
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
use App\Http\Controllers\Controller;

class BuildingDetailController extends Controller
{
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


        $features = BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'build_year' => $buildYear
            ]
        );

        $buildingType = BuildingType::find($request->get('building_type_id'));
        $features->buildingType()->associate($buildingType);

        $exampleBuilding = $this->getGenericExampleBuildingByBuildingType($buildingType);

        if ($exampleBuilding instanceof ExampleBuilding) {
            $building->exampleBuilding()->associate($exampleBuilding);
            $building->save();
	        ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);
        }

        // finish the step
        \Auth::user()->complete($this->step);
        return redirect()->route('cooperation.tool.general-data.index');

    }

    /**
     * Get a example building based on the building type
     *
     * @param BuildingType $buildingType
     * @return ExampleBuilding|\Illuminate\Database\Eloquent\Builder
     */
    private function getGenericExampleBuildingByBuildingType(BuildingType $buildingType)
    {
        $exampleBuilding = ExampleBuilding::generic()->where('building_type_id', $buildingType->id)->first();
        return $exampleBuilding;
    }

}
