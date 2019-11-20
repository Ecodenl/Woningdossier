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

        dd($buildingOwner->completedQuestionnaires);

        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();

        $buildingType = $building->getBuildingType(HoomdossierSession::getInputSource(true));

        $exampleBuildings = collect();

        if ($buildingType instanceof BuildingType) {
            $exampleBuildings = $cooperation->exampleBuildings()->where('building_type_id', '=', $buildingType->id)->get();
        }

        $myBuildingFeatures = $building->buildingFeatures()->forMe()->get();

        return view('cooperation.tool.general-data.building-characteristics.index', compact(
            'building', 'buildingOwner', 'buildingTypes', 'energyLabels', 'roofTypes', 'exampleBuildings', 'myBuildingFeatures',
            'prevBt', 'prevBy'
        ));
    }

    public function store(BuildingCharacteristicsFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $step = Step::findByShort('building-characteristics');

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

        return redirect($url);
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

}
