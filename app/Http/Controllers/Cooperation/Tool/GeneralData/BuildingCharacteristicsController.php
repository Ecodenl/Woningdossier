<?php

namespace App\Http\Controllers\Cooperation\Tool\GeneralData;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\GeneralData\BuildingCharacteristicsFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\Questionnaire;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\StepComment;
use App\Services\ExampleBuildingService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingCharacteristicsController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

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

//        dd($building->exampleBuilding->name);
        $myBuildingFeatures = $building->buildingFeatures()->forMe()->get();

        $prevBt = Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id') ?? '';
        $prevBy = Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year') ?? '';

        return view('cooperation.tool.general-data.building-characteristics.index', compact(
            'building', 'buildingOwner', 'buildingTypes', 'energyLabels', 'roofTypes', 'exampleBuildings', 'myBuildingFeatures',
            'prevBt', 'prevBy'
        ));
    }

    public function store(BuildingCharacteristicsFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true)->load('buildingFeatures');
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('building-characteristics');

        $buildYear = $request->input('building_features.build_year');
        $buildingTypeId = $request->input('building_features.building_type_id');
        $exampleBuildingId = $request->get('example_building_id', null);

        if (!is_null($exampleBuildingId)) {
            $exampleBuilding = ExampleBuilding::forMyCooperation()->where('id', $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        }

        // this has to be done before the new building features are saved
        $currentFeatures = $building->buildingFeatures;

        // save the data
        $building->buildingFeatures()->updateOrCreate([], $request->input('building_features'));
        StepCommentService::save($building, $inputSource, $step, $request->input('step_comments.comment'));
        StepHelper::complete($step, $building, $inputSource);
        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, $inputSource, $step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        dd('bier!', $building->exampleBuilding->name);
        return redirect($url);
    }


    /**
     * Store the bulding type id, when a user changes his building type id
     * after that selects a example building, the page will be reloaded.
     * but the type wasnt stored. now it is
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBuildingType(Request $request)
    {
        $buildingTypeId = $request->get('building_type_id');
        $building = HoomdossierSession::getBuilding(true);
        $building->buildingFeatures()->updateOrCreate([], ['building_type_id' => $buildingTypeId]);

        return response()->json();
    }

    /**
     * Retrieve the example buildings for a building type id
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function qualifiedExampleBuildings(Request $request)
    {
        $buildingType = BuildingType::findOrFail($request->get('building_type_id'));
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


    public function applyExampleBuilding(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $exampleBuildingId = $request->get('example_building_id');
        $buildYear = $request->get('build_year');

        if (! is_null($exampleBuildingId) && ! is_null($buildYear)) {
            $exampleBuilding = ExampleBuilding::forAnyOrMyCooperation()->where('id', $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
                ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

                return response()->json();
            }
        }
        // Something went wrong!
        return response()->json([], 500);
    }


}
