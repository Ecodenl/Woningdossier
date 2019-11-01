<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\User;
use App\Scopes\GetValueScope;
use App\Services\ExampleBuildingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingCharacteristicsController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        \DB::enableQueryLog();
        dd($buildingOwner->interests, \DB::getQueryLog());


        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();

        $buildingType = $building->getBuildingType(HoomdossierSession::getInputSource(true));
        $exampleBuildings = collect([]);
        if ($buildingType instanceof BuildingType) {
            $exampleBuildings = ExampleBuilding::forMyCooperation()
                ->where('building_type_id', '=', $buildingType->id)
                ->get();
        }

        $myBuildingFeatures = $building->buildingFeatures()->forMe()->get();

        $prevBt = Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id') ?? '';
        $prevBy = Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year') ?? '';

        return view('cooperation.tool.general-data.building-characteristics.index', compact(
            'building', 'buildingOwner', 'buildingTypes', 'energyLabels', 'roofTypes', 'exampleBuildings', 'myBuildingFeatures',
            'prevBt', 'prevBy'
        ));
    }

    public function qualifiedExampleBuildings(Request $request)
    {
        $buildingType = BuildingType::findOrFail($request->get('building_type'));
        $exampleBuildings = collect([]);
        if ($buildingType instanceof BuildingType) {
            // get the example buildings with translations so we can return it as a response
            $exampleBuildings = ExampleBuilding::forMyCooperation()
                ->where('building_type_id', '=', $buildingType->id)
                ->leftJoin('translations', 'example_buildings.name', '=', 'translations.key')
                ->where('translations.language', app()->getLocale())
                ->select('example_buildings.order', 'example_buildings.id', 'translations.translation')
                ->orderBy('order')
                ->get()
                ->toArray();
        }

        return response()->json($exampleBuildings);
    }

    public function store(Request $request)
    {
        return redirect(back());

        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $exampleBuildingId = $request->get('example_building_id', null);
        if (! is_null($exampleBuildingId)) {
            $exampleBuilding = ExampleBuilding::forMyCooperation()->where('id', $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        }

        $buildYear = $request->input('building_features.build_year');
        $buildingTypeId = $request->input('building_features.building_type_id');

        // this has to be done before the new building features are saved
        $currentFeatures = $building->buildingFeatures()->forInputSource($inputSource)->first();

        BuildingFeature::forMe()->updateOrCreate([
            'building_id' => $building->id,
            'input_source_id' => $inputSource->id,
        ], $request->input('building_features'));

        StepComment::forMe()->updateOrCreate([
            'building_id' => $building->id,
            'input_source_id' => $inputSource->id,
        ], $request->input('step_comments'));


        $this->handleExampleBuildingData($building, $currentFeatures, $buildYear, $buildingTypeId);

        return redirect(back());
    }

    /**
     * Method to handle the example building
     *
     * @param Building $building
     * @param BuildingFeature $currentFeatures
     * @param int $buildYear
     * @param int $buildingTypeId
     */
    private function handleExampleBuildingData(Building $building, BuildingFeature $currentFeatures, int $buildYear, int $buildingTypeId)
    {
        $buildingType = BuildingType::find($buildingTypeId);
        $exampleBuilding = $this->getGenericExampleBuildingByBuildingType($buildingType);

        // if there are no features yet, then we can apply the example building
        // else, we need to compare the old buildingtype and buildyear against that from the request, if those differ then we apply the example building again.
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
