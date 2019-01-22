<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\FloorInsulationCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FloorInsulationController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [4];
        /** @var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());

        $buildingInsulation = $building->getBuildingElement('floor-insulation');
        $buildingInsulationForMe = $building->getBuildingElementsForMe('floor-insulation');

        $floorInsulation = $buildingInsulation instanceof BuildingElement ? $buildingInsulation->element : null;

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

        $buildingElement = $building->buildingElements;
        $buildingElementsForMe = BuildingElement::forMe()->get();

        $buildingFeatures = $building->buildingFeatures;
        $buildingFeaturesForMe = BuildingFeature::forMe()->get();

        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation', 'buildingInsulation',
            'crawlspace', 'buildingCrawlspace', 'typeIds', 'buildingElementForMe', 'buildingFeaturesForMe', 'buildingElementsForMe',
            'crawlspacePresent', 'buildingFeatures', 'buildingElement', 'building', 'buildingInsulationForMe'
        ));
    }

    public function calculate(FloorInsulationFormRequest $request)
    {
        /**
         * @var Building
         */
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
        ];

        $crawlspace = Element::where('short', 'crawlspace')->first();

        $elements = $request->get('element', []);
        $buildingElements = $request->get('building_elements', []);
        $buildingFeatures = $request->get('building_features', []);

        $surface = array_key_exists('insulation_surface', $buildingFeatures) ? $buildingFeatures['insulation_surface'] : 0;

        if (array_key_exists('crawlspace', $buildingElements)) {
            // Check if crawlspace is accessible. If not: show warning!
            if (in_array($buildingElements['crawlspace'], ['unknown'])) {
                $result['crawlspace'] = 'warning';
            }
        }

        $crawlspaceValue = null;
        if (array_key_exists($crawlspace->id, $buildingElements)) {
            if (array_key_exists('element_value_id', $buildingElements[$crawlspace->id])) {
                $crawlspaceValue = ElementValue::where('element_id', $crawlspace->id)
                    ->where('id', $buildingElements[$crawlspace->id]['element_value_id'])
                    ->first();
            }
            if (array_key_exists('extra', $buildingElements[$crawlspace->id])) {
                // Check if crawlspace is accessible. If not: show warning!
                if (in_array($buildingElements[$crawlspace->id]['extra'], ['no', 'unknown'])) {
                    $result['crawlspace_access'] = 'warning';
                }
            }
        } else {
            // first page request
            $crawlspaceValue = $crawlspace->values()->orderBy('order')->first();
        }

        if ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 45) {
            $advice = Temperature::FLOOR_INSULATION_FLOOR;
        } elseif ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 30) {
            $advice = Temperature::FLOOR_INSULATION_BOTTOM;
        } else {
            $advice = Temperature::FLOOR_INSULATION_RESEARCH;
        }

        $insulationAdvice = MeasureApplication::byShort($advice);
        $result['insulation_advice'] = $insulationAdvice->measure_name;

        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        if (array_key_exists($floorInsulation->id, $elements)) {
            $floorInsulationValue = ElementValue::where('element_id', $floorInsulation->id)->where('id', $elements[$floorInsulation->id])->first();
            if ($floorInsulationValue instanceof ElementValue) {
                $result['savings_gas'] = FloorInsulationCalculator::calculateGasSavings($building, $floorInsulationValue, $user->energyHabit, $surface, $advice);
            }

            $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
            $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
            $result['cost_indication'] = Calculator::calculateCostIndication($surface, $insulationAdvice);
            $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
        }

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FloorInsulationFormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

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

        $interests = $request->input('interest', '');
        UserInterest::saveUserInterests($user, $interests);

        $buildingElements = $request->input('building_elements', '');
        $buildingElementId = array_keys($buildingElements)[1];

        $crawlspaceHasAccess = isset($buildingElements[$buildingElementId]['extra']) ? $buildingElements[$buildingElementId]['extra'] : '';
        $hasCrawlspace = isset($buildingElements['crawlspace']) ? $buildingElements['crawlspace'] : '';
        $heightCrawlspace = isset($buildingElements[$buildingElementId]['element_value_id']) ? $buildingElements[$buildingElementId]['element_value_id'] : '';
        $comment = $request->input('comment', '');

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
                    'comment' => $comment,
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
        $building->complete($this->step);
        ($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        $nextStep = StepHelper::getNextStep($this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        $user = Building::find(HoomdossierSession::getBuilding())->user;
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
