<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\FloorInsulation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FloorInsulationController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation)
    {
        $typeIds = [4];
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $buildingInsulation = $building->getBuildingElement('floor-insulation');
        $buildingInsulationForMe = $building->getBuildingElementsForMe('floor-insulation');

        $floorInsulation = optional($buildingInsulation)->element;

        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingCrawlspace = $building->getBuildingElement($crawlspace->short);

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


        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation', 'buildingInsulation', 'buildingInsulationForMe', 'buildingElementsOrderedOnInputSourceCredibility',
            'crawlspace', 'buildingCrawlspace', 'typeIds', 'buildingFeaturesOrderedOnInputSourceCredibility',
            'crawlspacePresent', 'buildingFeatures', 'buildingElement', 'building'
        ));
    }

    public function calculate(FloorInsulationFormRequest $request)
    {
        /**
         * @var Building
         */
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = FloorInsulation::calculate($building, HoomdossierSession::getInputSource(true), $user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $buildingId = $building->id;
        $inputSource = HoomdossierSession::getInputSource(true);
        $inputSourceId = $inputSource->id;

        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        // Get the value's from the input's
        $elements = $request->input('element', '');

        foreach ($elements as $elementId => $elementValueId) {
            BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'building_id' => $buildingId,
                    'element_id' => $elementId,
                    'input_source_id' => $inputSourceId,
                ],
                [
                    'element_value_id' => $elementValueId,
                ]
            );
        }

        $buildingElements = $request->input('building_elements', '');
        $buildingElementId = array_keys($buildingElements)[1];

        $crawlspaceHasAccess = isset($buildingElements[$buildingElementId]['extra']) ? $buildingElements[$buildingElementId]['extra'] : '';
        $hasCrawlspace = isset($buildingElements['crawlspace']) ? $buildingElements['crawlspace'] : '';
        $heightCrawlspace = isset($buildingElements[$buildingElementId]['element_value_id']) ? $buildingElements[$buildingElementId]['element_value_id'] : '';

        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'element_id' => $buildingElementId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'element_value_id' => $heightCrawlspace,
                'extra' => [
                    'has_crawlspace' => $hasCrawlspace,
                    'access' => $crawlspaceHasAccess,
                ],
            ]
        );
        $floorSurface = $request->input('building_features', '');

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'floor_surface' => isset($floorSurface['floor_surface']) ? $floorSurface['floor_surface'] : '0.0',
                'insulation_surface' => isset($floorSurface['insulation_surface']) ? $floorSurface['insulation_surface'] : '0.0',
            ]
        );

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (!empty($nextStep['tab_id'])) {
            $url .= '#' . $nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        $user = HoomdossierSession::getBuilding(true)->user;
        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        $elements = $request->input('element');

        if (array_key_exists($floorInsulation->id, $elements)) {
            $floorInsulationValue = ElementValue::where('element_id',
                $floorInsulation->id)->where('id',
                $elements[$floorInsulation->id])->first();
            // don't save if not applicable
            if ($floorInsulationValue instanceof ElementValue && $floorInsulationValue->calculate_value < 5) {
                /** @var JsonResponse $results */
                $results = $this->calculate($request);
                $results = $results->getData(true);

                if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
                    $measureApplication = MeasureApplication::translated('measure_name',
                        $results['insulation_advice'],
                        'nl')->first(['measure_applications.*']);
                    if ($measureApplication instanceof MeasureApplication) {
                        $actionPlanAdvice = new UserActionPlanAdvice($results);
                        $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                        $actionPlanAdvice->user()->associate($user);
                        $actionPlanAdvice->measureApplication()->associate($measureApplication);
                        $actionPlanAdvice->step()->associate($this->step);
                        $actionPlanAdvice->save();
                    }
                }
            }
        }
    }
}
