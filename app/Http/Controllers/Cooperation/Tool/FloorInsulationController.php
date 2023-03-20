<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\FloorInsulation;
use App\Helpers\Cooperation\Tool\FloorInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Http\Requests\Cooperation\Tool\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\Element;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Services\ConsiderableService;
use App\Services\Models\UserCostService;
use App\Services\StepCommentService;
use App\Services\ToolQuestionService;

class FloorInsulationController extends ToolController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(UserCostService $userCostService)
    {
        $typeIds = [4];
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $buildingInsulation = $building->getBuildingElement('floor-insulation', $this->masterInputSource);
        $buildingInsulationForMe = $building->getBuildingElementsForMe('floor-insulation');

        $floorInsulation = optional($buildingInsulation)->element;

        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingCrawlspace = $building->getBuildingElement($crawlspace->short, $this->masterInputSource);

        $crawlspacePresent = 2; // unknown
        if ($buildingCrawlspace instanceof \App\Models\BuildingElement) {
            if ($buildingCrawlspace->elementValue instanceof \App\Models\ElementValue) {
                $crawlspacePresent = 0; // yes
            }
        } else {
            $crawlspacePresent = 1; // now
        }

        $buildingElementsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingElements()->where('element_id', $crawlspace->id)
        )->get();

        $buildingFeaturesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingFeatures()
        )->get();

        $userCosts = $userCostService->user($building->user)
            ->inputSource(HoomdossierSession::getInputSource(true))
            ->forAdvisable(Step::findByShort('floor-insulation'))
            ->getAnswers(true);

        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation', 'buildingInsulation', 'buildingInsulationForMe',
            'buildingElementsOrderedOnInputSourceCredibility',
            'crawlspace', 'buildingCrawlspace', 'typeIds', 'buildingFeaturesOrderedOnInputSourceCredibility',
            'crawlspacePresent', 'building', 'userCosts'
        ));
    }

    public function calculate(FloorInsulationFormRequest $request)
    {
        /**
         * @var Building
         */
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = FloorInsulation::calculate(
            $building,
            $this->masterInputSource,
            $user->energyHabit()->forInputSource($this->masterInputSource)->first(),
            $request->all()
        );

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request, UserCostService $userCostService, ToolQuestionService $toolQuestionService)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $considerables = $request->validated()['considerables'];
        ConsiderableService::save($this->step, $user, $inputSource, $considerables[$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $userCosts = $request->validated()['user_costs'];
        $userCostService->user($user)->inputSource($inputSource);
        $userCostValues = [];
        $executeHow = $request->validated()['execute'];
        $toolQuestionService->building($building)->currentInputSource($inputSource);
        if ($considerables[$this->step->id]['is_considering'] && ($request->validated()['building_elements']['extra']['has_crawlspace'] ?? null) !== 'no') {
            $crawlSpace = Element::findByShort('crawlspace');
            $crawlSpaceHigh = $crawlSpace->elementValues()->where('calculate_value', 45)->first();
            $crawlSpaceMid = $crawlSpace->elementValues()->where('calculate_value', 30)->first();

            $idMap = [
                $crawlSpaceHigh->id => Temperature::FLOOR_INSULATION_FLOOR,
                $crawlSpaceMid->id => Temperature::FLOOR_INSULATION_BOTTOM,
            ];

            $research = Temperature::FLOOR_INSULATION_RESEARCH;

            $height = $request->validated()['building_elements']['element_value_id'] ?? null;
            $advice = $idMap[$height] ?? $research;

            foreach ($userCosts as $measureShort => $costData) {
                // Only save for connected type
                if ($measureShort === $advice) {
                    $measureApplication = MeasureApplication::findByShort($measureShort);
                    $userCostService->forAdvisable($measureApplication)->sync($costData);
                    $userCostValues[$measureShort] = $costData;
                }
            }
            foreach ($executeHow as $measureShort => $howData) {
                // Only save for connected type
                if ($measureShort === $advice) {
                    $toolQuestionService->toolQuestion(ToolQuestion::findByShort("execute-{$measureShort}-how"))
                        ->save($howData['how']);
                }
            }
        }

        $dirtyAttributes = json_decode($request->input('dirty_attributes'), true);
        $updatedMeasureIds = [];
        // If anything's dirty, all measures must be recalculated
        if (! empty($dirtyAttributes)) {
            $updatedMeasureIds = MeasureApplication::findByShorts([
                'floor-insulation', 'bottom-insulation', 'floor-insulation-research',
            ])
                ->pluck('id')
                ->toArray();
        }

        $values = $request->validated();
        $values['updated_measure_ids'] = $updatedMeasureIds;
        $values['user_costs'] = $userCostValues;

        (new FloorInsulationHelper($user, $inputSource))
            ->setValues($values)
            ->saveValues()
            ->createAdvices();

        return $this->completeStore($this->step, $building, $inputSource);
    }
}
