<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\InsulatedGlazingCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Models\WoodRotStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; use App\Scopes\GetValueScope;
use Illuminate\Support\Facades\Auth;

class InsulatedGlazingController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get the next page order
        $nextPage = $this->step->order + 1;

        // we do not want the user to set his interests for this step
//        $typeIds = [1, 2];

//        StepHelper::getNextStep();

        /**
         * @var Building
         */
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $steps = Step::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        $insulatedGlazings = InsulatingGlazing::all();
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();


        $measureApplicationShorts = [
            'glass-in-lead',
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
        ];

        $buildingInsulatedGlazings = [];
        $buildingInsulatedGlazingsForMe = [];

        $buildingFeaturesForMe = $building->buildingFeatures->forMe()->get();
        $userInterests = [];

        foreach ($measureApplicationShorts as $measureApplicationShort) {
            $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

            if ($measureApplication instanceof MeasureApplication) {
                // get current situation
                $currentInsulatedGlazing = $building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id)->first();

                $currentInsulatedGlazingInput = BuildingInsulatedGlazing::where('measure_application_id', $measureApplication->id)->forMe()->get();

                if (!$currentInsulatedGlazingInput->isEmpty()) {
                    $buildingInsulatedGlazingsForMe[$measureApplication->id] = $currentInsulatedGlazingInput;
                }
                if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                    $buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
                }
                // get interests fo3r the measure
                $measureInterest = $user->interests()
                    ->where('interested_in_type', 'measure_application')
                    ->where('interested_in_id', $measureApplication->id)
                    ->get();

                if ($measureInterest instanceof UserInterest) {
                    // We only have to check on the interest ID, so we don't put
                    // full objects in the array
                    $userInterests[$measureApplication->id] = $measureInterest->interest_id;
                }

                $measureApplications[] = $measureApplication;
            }
        }

//        $inputValues = $woodElements;
//
//        $x = $building;
//        $z = $building->buildingElements()->forMe();
//
//        foreach ($inputValues->values()->orderBy('order')->get() as $i => $inputValue) {
//            // returned 1 instance 2 null
//            dump($z->where('element_id', $inputValues->id)->where('element_value_id', $inputValue->id)->first());
//            // returned 3 instances 0 null
//            dump($x->buildingElements()->forMe()->where('element_id', $inputValues->id)->where('element_value_id', $inputValue->id)->first());
//        }


        $myBuildingElements = BuildingElement::forMe()->get();

        return view('cooperation.tool.insulated-glazing.index', compact(
            'building', 'steps', 'interests', 'myBuildingElements',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
            'userInterests', 'crackSealing', 'frames', 'woodElements', 'buildingFeaturesForMe',
            'paintworkStatuses', 'woodRotStatuses', 'buildingInsulatedGlazingsForMe'
        ));
    }


    protected function saveAdvices(Request $request)
    {
        $user = Building::find(HoomdossierSession::getBuilding())->user;
        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        foreach ($results['measure'] as $measureId => $data) {
            if (array_key_exists('costs', $data) && $data['costs'] > 0) {
                $measureApplication = MeasureApplication::where('id',
                    $measureId)->where('step_id', $this->step->id)->first();

                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($data);
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
        }

        $keysToMeasure = [
            'paintwork' => 'paint-wood-elements',
            'crack-sealing' => 'crack-sealing',
        ];

        foreach ($keysToMeasure as $key => $measureShort) {
            if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0) {
                $measureApplication = MeasureApplication::where('short', $measureShort)->first();
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results[$key]);
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
        }
    }

    public function calculate(Request $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'measure' => [],
        ];

        $userInterests = $request->get('user_interests', []);

        foreach ($request->get('building_insulated_glazings', []) as $measureApplicationId => $buildingInsulatedGlazingsData) {
            $measureApplication = MeasureApplication::find($measureApplicationId);
            $buildingHeatingId = array_key_exists('building_heating_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['building_heating_id'] : 0;
            $buildingHeating = BuildingHeating::find($buildingHeatingId);
            $insulatedGlazingId = array_key_exists('insulated_glazing_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['insulated_glazing_id'] : 0;
            $insulatedGlazing = InsulatingGlazing::find($insulatedGlazingId);
            $interestId = array_key_exists($measureApplicationId, $userInterests) ? $userInterests[$measureApplicationId] : 0;
            $interest = Interest::find($interestId);

            if ($measureApplication instanceof MeasureApplication &&
                $buildingHeating instanceof BuildingHeating &&
                $interest instanceof Interest &&
                array_key_exists($measureApplicationId, $userInterests) && $userInterests[$measureApplicationId] <= 3) {
                $gasSavings = InsulatedGlazingCalculator::calculateGasSavings(
                    (int) $buildingInsulatedGlazingsData['m2'], $measureApplication,
                    $buildingHeating, $insulatedGlazing
                );

                $result['measure'][$measureApplication->id] = [
                    'costs' => InsulatedGlazingCalculator::calculateCosts($measureApplication, $interest, (int) $buildingInsulatedGlazingsData['m2'], (int) $buildingInsulatedGlazingsData['windows']),
                    'savings_gas' => $gasSavings,
                    'savings_co2' => Calculator::calculateCo2Savings($gasSavings),
                    'savings_money' => Calculator::calculateMoneySavings($gasSavings),
                ];

                $result['cost_indication'] += $result['measure'][$measureApplication->id]['costs'];
                $result['savings_gas'] += $gasSavings;

                $result['savings_co2'] += $result['measure'][$measureApplication->id]['savings_co2'];
                $result['savings_money'] += $result['measure'][$measureApplication->id]['savings_money'];
            }
        }

        $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

        $result['paintwork'] = [
            'costs' => 0,
            'year' => null,
        ];

        $frames = Element::where('short', 'frames')->first();
        $buildingElements = $request->get('building_elements', []);
        $framesValueId = 0;
        if (array_key_exists($frames->id, $buildingElements) && array_key_exists('frames', $buildingElements[$frames->id])) {
            $framesValueId = (int) $buildingElements[$frames->id]['frames'];
        }
        $frameElementValue = ElementValue::find($framesValueId);

        // only applies for wooden frames
        if ($frameElementValue instanceof ElementValue && 'frames' == $frameElementValue->element->short/* && $frameElementValue->calculate_value > 0*/) {
            $windowSurface = $request->get('window_surface', 0);
            // frame type use used as ratio (e.g. wood + some others -> use 70% of surface)
            $woodElementValues = [];

            foreach ($buildingElements as $short => $serviceIds) {
                if ('wood-elements' == $short) {
                    foreach ($serviceIds as $serviceId => $ids) {
                        foreach (array_keys($ids) as $id) {
                            $woodElementValue = ElementValue::where('id', $id)->where('element_id',
                                $serviceId)->first();

                            if ($woodElementValue instanceof ElementValue && $woodElementValue->element->short == $short) {
                                $woodElementValues[] = $woodElementValue;
                            }
                        }
                    }
                }
            }

            $measureApplication = MeasureApplication::where('short', 'paint-wood-elements')->first();

            $number = InsulatedGlazingCalculator::calculatePaintworkSurface($frameElementValue, $woodElementValues, $windowSurface);

            $buildingPaintworkStatuses = $request->get('building_paintwork_statuses', []);
            $paintworkStatus = null;
            $woodRotStatus = null;
            $lastPaintedYear = 2000;
            if (array_key_exists('paintwork_status_id', $buildingPaintworkStatuses)) {
                $paintworkStatus = PaintworkStatus::find($buildingPaintworkStatuses['paintwork_status_id']);
            }
            if (array_key_exists('wood_rot_status_id', $buildingPaintworkStatuses)) {
                $woodRotStatus = WoodRotStatus::find($buildingPaintworkStatuses['wood_rot_status_id']);
            }
            if (array_key_exists('last_painted_year', $buildingPaintworkStatuses)) {
                $lastPaintedYear = $buildingPaintworkStatuses['last_painted_year'];
            }

            $year = InsulatedGlazingCalculator::determineApplicationYear($measureApplication, $paintworkStatus, $woodRotStatus, $lastPaintedYear);

            $costs = Calculator::calculateMeasureApplicationCosts($measureApplication,
                $number,
                $year);
            $result['paintwork'] = compact('costs', 'year');
        }

        $result['crack-sealing'] = [
            'cost' => 0,
            'savings' => 0,
        ];

        $crackSealingId = $request->get('building_elements.crack-sealing', 0);
        $crackSealingElement = ElementValue::find($crackSealingId);
        if ($crackSealingElement instanceof ElementValue && 'crack-sealing' == $crackSealingElement->element->short && $crackSealingElement->calculate_value > 1) {
            $energyHabit = $user->energyHabits;
            $gas = 0;
            if ($energyHabit instanceof UserEnergyHabit) {
                $gas = $energyHabit->amount_gas;
            }
            if (2 == $crackSealingElement->calculate_value) {
                $result['crack-sealing']['savings'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING / 100) * $gas;
            } else {
                $result['crack-sealing']['savings'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING / 100) * $gas;
            }

            $measureApplication = MeasureApplication::where('short', 'crack-sealing')->first();

            $result['crack-sealing']['costs'] = Calculator::calculateMeasureApplicationCosts($measureApplication, 1);
        }

        return response()->json($result);
    }

    /**
     * Store the incoming request and redirect to the next step.
     *
     * @param InsulatedGlazingFormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InsulatedGlazingFormRequest $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $buildingInsulatedGlazings = $request->input('building_insulated_glazings', '');

        // Saving the insulate glazings
        $interests = collect();
        foreach ($buildingInsulatedGlazings as $measureApplicationId => $buildingInsulatedGlazing) {
            $insulatedGlazingId = $buildingInsulatedGlazing['insulated_glazing_id'];
            $buildingHeatingId = $buildingInsulatedGlazing['building_heating_id'];
            $m2 = isset($buildingInsulatedGlazing['m2']) ? $buildingInsulatedGlazing['m2'] : 0;
            $windows = isset($buildingInsulatedGlazing['windows']) ? $buildingInsulatedGlazing['windows'] : 0;

            // The interest for a measure
            $userInterestId = $request->input('user_interests.'.$measureApplicationId.'');
            // Update or Create the buildingInsulatedGlazing
            BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'building_id' => $buildingId,
                    'input_source_id' => $inputSourceId,
                    'measure_application_id' => $measureApplicationId,
                ],
                [
                    'insulating_glazing_id' => $insulatedGlazingId,
                    'building_heating_id' => $buildingHeatingId,
                    'm2' => $m2,
                    'windows' => $windows,
                    'extra' => ['comment' => $request->input('comment', '')],
                ]
            );
            // We'll create the user interests for the measures or update it
            UserInterest::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'interested_in_type' => 'measure_application',
                    'interested_in_id' => $measureApplicationId,
                ],
                [
                    'interest_id' => $userInterestId,
                ]
            );
            // collect all the selected interests
            $interests->push(Interest::find($userInterestId));
        }

        // get the highest interest level (which is the lowst calculate value.)
        $highestInterestLevel = $interests->unique('id')->min('calculate_value');
        // update the livingroomwindow interest level based of the highest interest level for the measure.
        $livingRoomWindowsElement = Element::where('short', 'living-rooms-windows')->first();
        UserInterest::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id'            => $user->id,
                'interested_in_type' => 'element',
                'interested_in_id'   => $livingRoomWindowsElement->id,
            ],
            [
                'interest_id'        => $highestInterestLevel,
            ]
        );

        // saving the main building elements
        $elements = $request->input('building_elements', []);
        foreach ($elements as $elementId => $elementValueId) {
            $element = Element::find($elementId);
            $elementValue = ElementValue::find(reset($elementValueId));

            if ($element instanceof Element && $elementValue instanceof ElementValue) {
                BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                    [
                        'element_id' => $element->id,
                        'input_source_id' => $inputSourceId,
                        'building_id' => $buildingId
                    ],
                    [
                        'element_value_id' => $elementValue->id,
                    ]
                );
            }
        }

        // Saving the wood building elements
        // Get the wood elements
        $woodElements = $request->input('building_elements.wood-elements.*.*');

        if (isset($woodElements)) {
            // Get the first key for the woodElementId
            $woodElementId = key($request->input('building_elements.wood-elements'));

            // Check if there are wood elements drop them
            if (BuildingElement::where('element_id', $woodElementId)->where('building_id', $buildingId)->where('input_source_id', $inputSourceId)->count() > 0) {
                BuildingElement::where('element_id', $woodElementId)
                    ->where('building_id', $buildingId)
                    ->where('input_source_id', $inputSourceId)
                    ->delete();
            }

            // Save the woodElements
            foreach ($woodElements as $woodElementValueId) {
                BuildingElement::create(
                    [
                        'building_id' => $buildingId,
                        'input_source_id' => $inputSourceId,
                        'element_id' => $woodElementId,
                        'element_value_id' => $woodElementValueId,
                    ]
                );
            }
        }

        // Save the paintwork statuses
        $paintWorkStatuses = $request->get('building_paintwork_statuses', '');
        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId
            ],
            [
                'last_painted_year' => $paintWorkStatuses['last_painted_year'],
                'paintwork_status_id' => $paintWorkStatuses['paintwork_status_id'],
                'wood_rot_status_id' => $paintWorkStatuses['paintwork_status_id'],
            ]
        );

        // Save the window surface to the building feature
        $windowSurface = $request->get('window_surface', '');
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId
            ],
            [
                'window_surface' => $windowSurface
            ]
        );

        $this->saveAdvices($request);
        // Save progress
        $user->complete($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
    }
}
