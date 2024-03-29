<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\UserToolDataChanged;
use App\Helpers\Arr;
use App\Helpers\Cooperation\Tool\SolarPanelHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\SolarPanelFormRequest;
use App\Models\MeasureApplication;
use App\Models\PvPanelOrientation;
use App\Models\Service;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
use App\Services\LegacyService;
use App\Services\StepCommentService;
use App\Services\ToolQuestionService;
use Illuminate\Http\Request;

class SolarPanelsController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(LegacyService $legacyService)
    {
        $typeIds = [7];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $pvPanelOrientations = PvPanelOrientation::orderBy('order')->get();

        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();

        $pvPanelsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->pvPanels()
        )->get();


        $totalSolarPanelService = Service::findByShort('total-sun-panels');
        $totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingServices()->where('service_id', $totalSolarPanelService->id)
        )->get();


        $hasSolarPanelsToolQuestion = ToolQuestion::findByShort('has-solar-panels');
        $hasSolarAnswersOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $hasSolarPanelsToolQuestion->toolQuestionAnswers()
                ->allInputSources()
                ->with('inputSource')
                ->where('building_id', $building->id)
        )->get();

        $measureRelatedAnswers = $legacyService->user($building->user)
            ->inputSource(HoomdossierSession::getInputSource(true))
            ->getMeasureRelatedAnswers(Step::findByShort('solar-panels'));

        return view('cooperation.tool.solar-panels.index',
            compact(
                'building', 'pvPanelOrientations', 'buildingOwner', 'typeIds', 'totalSolarPanelService',
                'energyHabitsOrderedOnInputSourceCredibility', 'pvPanelsOrderedOnInputSourceCredibility', 'totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility',
                'hasSolarPanelsToolQuestion', 'hasSolarAnswersOrderedOnInputSourceCredibility', 'measureRelatedAnswers'
            )
        );
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $result = SolarPanel::calculate($building, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Cooperation\Tool\SolarPanelFormRequest  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SolarPanelFormRequest $request, LegacyService $legacyService, ToolQuestionService $toolQuestionService)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $considerables = $request->validated()['considerables'];
        ConsiderableService::save($this->step, $user, $inputSource, $considerables[$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $toolQuestionService->building($building)->currentInputSource($inputSource);
        $measureRelatedShorts = $legacyService->getToolQuestionShorts(Step::findByShort('solar-panels'));
        // Only one. Save if considering
        if ($considerables[$this->step->id]['is_considering']) {
            foreach ($measureRelatedShorts as $measureId => $tqShorts) {
                foreach ($tqShorts as $tqShort) {
                    // Subsidy question might have been removed and thus not saveable.
                    if (array_key_exists($tqShort, $request->validated())) {
                        $tq = ToolQuestion::findByShort($tqShort);
                        $toolQuestionService->toolQuestion($tq)->save($request->validated()[$tqShort]);
                    }
                }
            }
        }

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];
        // If anything's dirty, all measures must be recalculated (we can't really check specifics here)
        if (! empty($dirtyAttributes)) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'solar-panels-place-replace',
            ])
                ->pluck('id')
                ->toArray();

            UserToolDataChanged::dispatch($user);
        }

        $toolQuestionService->building($building)
            ->currentInputSource($inputSource);
        // now attempt to save the "dynamic" questions.
        foreach ($request->validated()['filledInAnswers'] as $toolQuestionId => $givenAnswer) {
            $toolQuestionService->toolQuestion(ToolQuestion::find($toolQuestionId))
                ->saveToolQuestionCustomValues($givenAnswer);
        }

        $values = $request->only('building_pv_panels', 'user_energy_habits', 'considerables', 'building_services');

        // As of right now, values are not dynamically updated. Therefore, if the filled in answer for the solar panels
        // is set to "no", we will nullify related questions.
        $hasSolarPanelsToolQuestion = ToolQuestion::findByShort('has-solar-panels');
        $answer = $request->validated()['filledInAnswers'][$hasSolarPanelsToolQuestion->id] ?? null;
        if ($answer === 'no') {
            Arr::set($values, 'building_services.7.extra.year', null);
            Arr::set($values, 'building_services.7.extra.value', null);
            Arr::set($values, 'building_pv_panels.total_installed_power', null);
        }
        $values['updated_measure_ids'] = $updatedMeasureIds;
        
        (new SolarPanelHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
