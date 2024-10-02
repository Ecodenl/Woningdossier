<?php

namespace App\Http\Controllers\Cooperation\Tool;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Events\UserToolDataChanged;
use App\Helpers\Arr;
use App\Helpers\Cooperation\Tool\RoofInsulationHelper;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoofInsulation;
use App\Helpers\StepHelper;
use App\Helpers\Str;
use App\Http\Requests\Cooperation\Tool\RoofInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
use App\Services\LegacyService;
use App\Services\StepCommentService;
use App\Services\ToolQuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoofInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index(LegacyService $legacyService): View
    {
        $typeIds = [5];

        /** var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();

        $primaryRoofTypes = RoofType::orderBy('order')->get();
        $secondaryRoofTypes = $primaryRoofTypes->whereIn('short', RoofType::SECONDARY_ROOF_TYPE_SHORTS);

        $currentRoofTypes = $building->roofTypes()->forInputSource($this->masterInputSource)->get();
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
                if (!empty($cat)) {
                    $currentCategorizedRoofTypes[$cat] = $currentRoofType->toArray();
                }
            }

            foreach ($currentRoofTypesForMe as $currentRoofTypeForMe) {
                $cat = RoofInsulation::getRoofTypeCategory($currentRoofTypeForMe->roofType);
                if (!empty($cat)) {
                    // we do not want this to be an array, otherwise we would have to add additional functionality to the input group component.
                    $currentCategorizedRoofTypesForMe[$cat][] = $currentRoofTypeForMe;
                }
            }
        }

        $measureRelatedAnswers = $legacyService->user($building->user)
            ->inputSource(HoomdossierSession::getInputSource(true))
            ->getMeasureRelatedAnswers(Step::findByShort('roof-insulation'));

        $measureRelatedAnswersCategorized = [];

        foreach (RoofInsulation::getMeasureApplicationsAdviceMap() as $cat => $measures) {
            foreach ($measures as $measureApplication) {
                $measureRelatedAnswersCategorized[$cat][$measureApplication->id] = $measureRelatedAnswers[$measureApplication->id];
            }
        }

        return view('cooperation.tool.roof-insulation.index', compact(
            'building', 'primaryRoofTypes', 'secondaryRoofTypes', 'typeIds',
            'buildingFeaturesForMe', 'currentRoofTypes', 'roofTileStatuses', 'roofInsulation', 'currentRoofTypesForMe',
            'heatings', 'measureApplications', 'currentCategorizedRoofTypes', 'currentCategorizedRoofTypesForMe',
            'measureRelatedAnswersCategorized'
        ));
    }

    public function calculate(Request $request): JsonResponse
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $result = \App\Calculations\RoofInsulation::calculate($building,
            $this->masterInputSource, $building->user->energyHabit()->forInputSource($this->masterInputSource)->first(), $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RoofInsulationFormRequest $request, LegacyService $legacyService, ToolQuestionService $toolQuestionService)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $considerables = $request->validated()['considerables'];
        ConsiderableService::save($this->step, $user, $inputSource, $considerables[$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $toolQuestionService->building($building)->currentInputSource($inputSource);
        $measureRelatedShorts = $legacyService->getToolQuestionShorts(Step::findByShort('roof-insulation'));
        if ($considerables[$this->step->id]['is_considering']) {
            $measureIds = array_filter(Arr::pluck($request->validated()['building_roof_types'], 'extra.measure_application_id'));

            foreach ($measureRelatedShorts as $measureId => $tqShorts) {
                if (in_array($measureId, $measureIds)) {
                    foreach ($tqShorts as $tqShort) {
                        // Subsidy question might have been removed and thus not saveable.
                        if (array_key_exists($tqShort, $request->validated())) {
                            $tq = ToolQuestion::findByShort($tqShort);
                            $toolQuestionService->toolQuestion($tq)->save($request->validated()[$tqShort]);
                        }
                    }
                }
            }
        }

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        if (!empty($dirtyAttributes)) {
            UserToolDataChanged::dispatch($user);
        }
        $dirtyNames = array_keys($dirtyAttributes);
        $updatedMeasureIds = [];

        $dirtyPitched = Str::arrStartsWith($dirtyNames, 'building_roof_types[pitched]');
        $dirtyFlat = Str::arrStartsWith($dirtyNames, 'building_roof_types[flat]');

        // We update everything if the primary roof is changed, otherwise we will update the relevant measures for the
        // changed roof category
        if (Str::arrStartsWith($dirtyNames, 'building_features')
            || Str::arrStartsWith($dirtyNames, 'building_roof_type_ids') || ($dirtyFlat && $dirtyPitched)
        ) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles',
                'roof-insulation-flat-current', 'roof-insulation-flat-replace-current',
                'replace-tiles', 'replace-roof-insulation',
                //'replace-zinc-pitched',
                //'replace-zinc-flat',
            ])->pluck('id')->toArray();
        } else {
            if ($dirtyFlat) {
                $updatedMeasureIds = MeasureApplication::findByShorts([
                    'roof-insulation-flat-current', 'roof-insulation-flat-replace-current', 'replace-roof-insulation',
                    //'replace-zinc-flat',
                ])->pluck('id')->toArray();
            } elseif ($dirtyPitched) {
                $updatedMeasureIds = MeasureApplication::findByShorts([
                    'roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles',
                    'replace-tiles',
                    //'replace-zinc-pitched',
                ])->pluck('id')->toArray();
            }
        }

        $values = $request->only('considerables', 'building_roof_type_ids', 'building_features',
            'building_roof_types', 'step_comments');
        $values['updated_measure_ids'] = $updatedMeasureIds;

        // Usually we let the completeStore function handle the completion, but we NEED the step to be completed
        // BEFORE we can calculate the roof insulation advices.
        StepHelper::complete($this->step, $building, $inputSource);

        (new RoofInsulationHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
