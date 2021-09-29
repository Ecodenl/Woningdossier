<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Cooperation\Tool\RoofInsulationHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoofInsulation;
use App\Http\Requests\Cooperation\Tool\RoofInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoofInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [5];

        /** var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        /** var BuildingFeature $features */
        $features = $building->buildingFeatures;
        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        $roofTypes = RoofType::findByShorts(RoofType::SECONDARY_ROOF_TYPE_SHORTS);

        $currentRoofTypes = $building->roofTypes;
        $currentRoofTypesForMe = $building->roofTypes()->forMe()->get();

        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $heatings = BuildingHeating::all();
        $measureApplications = RoofInsulation::getMeasureApplicationsAdviceMap();

        $currentCategorizedRoofTypes = [
            'flat' => [],
            'pitched' => [],
        ];

        $currentCategorizedRoofTypesForMe = [
            'flat' => [],
            'pitched' => [],
        ];

        if ($currentRoofTypes instanceof Collection) {
            /** var BuildingRoofType $currentRoofType */
            foreach ($currentRoofTypes as $currentRoofType) {
                $cat = RoofInsulation::getRoofTypeCategory($currentRoofType->roofType);
                if (! empty($cat)) {
                    $currentCategorizedRoofTypes[$cat] = $currentRoofType->toArray();
                }
            }

            foreach ($currentRoofTypesForMe as $currentRoofTypeForMe) {
                $cat = RoofInsulation::getRoofTypeCategory($currentRoofTypeForMe->roofType);
                if (! empty($cat)) {
                    // we do not want this to be an array, otherwise we would have to add additional functionality to the input group component.
                    $currentCategorizedRoofTypesForMe[$cat][] = $currentRoofTypeForMe;
                }
            }
        }

        return view('cooperation.tool.roof-insulation.index', compact(
            'building', 'features', 'roofTypes', 'typeIds', 'buildingFeaturesForMe',
             'currentRoofTypes', 'roofTileStatuses', 'roofInsulation', 'currentRoofTypesForMe',
             'heatings', 'measureApplications', 'currentCategorizedRoofTypes', 'currentCategorizedRoofTypesForMe'));
    }

    public function calculate(Request $request)
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $result = \App\Calculations\RoofInsulation::calculate($building, HoomdossierSession::getInputSource(true), $building->user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RoofInsulationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        ConsiderableService::save($this->step, $user, $inputSource, $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        (new RoofInsulationHelper($user, $inputSource))
            ->setValues($request->all())
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
