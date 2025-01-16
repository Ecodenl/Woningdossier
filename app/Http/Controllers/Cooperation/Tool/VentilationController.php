<?php

namespace App\Http\Controllers\Cooperation\Tool;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Calculations\Ventilation;
use App\Events\UserToolDataChanged;
use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\VentilationFormRequest;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
use App\Services\LegacyService;
use App\Services\StepCommentService;
use App\Services\ToolQuestionService;
use Illuminate\Http\Request;

class VentilationController extends ToolController
{
    public function index(LegacyService $legacyService): View
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $building = HoomdossierSession::getBuilding(true);

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation', $masterInputSource);

        /** @var ServiceValue $buildingVentilation */
        $buildingVentilation = $buildingVentilationService->serviceValue;

        $howValues = VentilationHelper::getHowValues();
        $livingSituationValues = VentilationHelper::getLivingSituationValues();
        $usageValues = VentilationHelper::getUsageValues();

        $measureRelatedAnswers = $legacyService->user($building->user)
            ->inputSource(HoomdossierSession::getInputSource(true))
            ->getMeasureRelatedAnswers(Step::findByShort('ventilation'));

        return view('cooperation.tool.ventilation.index', compact(
            'building',
            'buildingVentilation',
            'howValues',
            'livingSituationValues',
            'usageValues',
            'measureRelatedAnswers',
        ));
    }

    public function store(VentilationFormRequest $request, LegacyService $legacyService, ToolQuestionService $toolQuestionService): RedirectResponse
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $step = Step::findByShort('ventilation');

        // the actually checked considerables, so these are considered true
        $considerables = $request->input('considerables', []);

        // now get the measure applications the user did not check (so does not consider)
        $notConsiderableMeasureApplications = $step->measureApplications()->whereNotIn('id', array_keys($considerables))->get();
        // collect them al into one array, the VentilationHelper expects this format.
        foreach ($notConsiderableMeasureApplications as $measureApplication) {
            $considerables[$measureApplication->id] = ['is_considering' => false];
        }

        foreach ($considerables as $considerableId => $considerableData) {
            ConsiderableService::save(MeasureApplication::findOrFail($considerableId), $buildingOwner, $inputSource, $considerableData);
        }

        $toolQuestionService->building($building)->currentInputSource($inputSource);
        $measureRelatedShorts = $legacyService->getToolQuestionShorts(Step::findByShort('ventilation'));
        foreach ($measureRelatedShorts as $measureId => $tqShorts) {
            if ($considerables[$measureId]['is_considering']) {
                foreach ($tqShorts as $tqShort) {
                    // Subsidy question might have been removed and thus not saveable.
                    if (array_key_exists($tqShort, $request->validated())) {
                        $tq = ToolQuestion::findByShort($tqShort);
                        $toolQuestionService->toolQuestion($tq)->save($request->validated()[$tqShort]);
                    }
                }
            }
        }

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $step, $stepComments['comment']);

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        if (! empty($dirtyAttributes)) {
            UserToolDataChanged::dispatch($buildingOwner);
        }
        $updatedMeasureIds = [];

        $values = $request->only('building_ventilations');
        $values['considerables'] = $considerables;
        $values['updated_measure_ids'] = $updatedMeasureIds;

        (new VentilationHelper($buildingOwner, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }

    public function calculate(Request $request): JsonResponse
    {
        /** @var \App\Models\Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $result = Ventilation::calculate($building, $this->masterInputSource, $request->all());

        return response()->json($result);
    }
}
