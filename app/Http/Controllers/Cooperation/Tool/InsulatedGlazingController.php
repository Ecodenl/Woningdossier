<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\InsulatedGlazing;
use App\Events\StepDataHasBeenChangedEvent;
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
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        // we do not want the user to set his interests for this step
//        $typeIds = [1, 2];

//        StepHelper::getNextStep();

        /**
         * @var Building
         */
        $building = Building::find(HoomdossierSession::getBuilding());
        $buildingOwner = $building->user;

        $interests = Interest::orderBy('order')->get();

        $insulatedGlazings = InsulatingGlazing::all();
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        $measureApplicationShorts = [
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
            'glass-in-lead',
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
                $currentInsulatedGlazingInputs = BuildingInsulatedGlazing::where('measure_application_id', $measureApplication->id)->forMe()->get();

                if (! $currentInsulatedGlazingInputs->isEmpty()) {
                    $buildingInsulatedGlazingsForMe[$measureApplication->id] = $currentInsulatedGlazingInputs;
                }
                if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                    $buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
                }
                // get interests for the measure
                $measureInterest = $buildingOwner->interests()
                    ->where('interested_in_type', 'measure_application')
                    ->where('interested_in_id', $measureApplication->id)
                    ->first();

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
        $userInterestsForMe = UserInterest::forMe()->where('interested_in_type', 'measure_application')->get();

        return view('cooperation.tool.insulated-glazing.index', compact(
            'building', 'interests', 'myBuildingElements', 'buildingOwner', 'userInterestsForMe',
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

        $result = InsulatedGlazing::calculate($building, $user, $request->all());

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
                    'input_source_id'    => $inputSourceId,
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
                'input_source_id'    => $inputSourceId,
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
                        'building_id' => $buildingId,
                    ],
                    [
                        'element_value_id' => $elementValue->id,
                    ]
                );
            }
        }

        $woodElements = $request->input('building_elements.wood-elements', []);

        $woodElementCreateData = [];
        foreach ($woodElements as $woodElementId => $woodElementValueIds) {
            // add the data we need to perform a create
            foreach ($woodElementValueIds as $woodElementValueId) {
                array_push($woodElementCreateData, ['element_value_id' => $woodElementValueId]);
            }

            ModelService::deleteAndCreate(BuildingElement::class,
                [
                    'building_id' => $buildingId,
                    'element_id' => $woodElementId,
                    'input_source_id' => $inputSourceId,
                ],
                $woodElementCreateData
            );
        }

        // Save the paintwork statuses
        $paintWorkStatuses = $request->get('building_paintwork_statuses', '');

        $lastPaintedYear = 2000;
        if (array_key_exists('last_painted_year', $paintWorkStatuses)) {
            $year = (int) $paintWorkStatuses['last_painted_year'];
            if ($year > 1950) {
                $lastPaintedYear = $year;
            }
        }

        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'last_painted_year' => $lastPaintedYear,
                'paintwork_status_id' => $paintWorkStatuses['paintwork_status_id'],
                'wood_rot_status_id' => $paintWorkStatuses['paintwork_status_id'],
            ]
        );

        // Save the window surface to the building feature
        $windowSurface = $request->get('window_surface', '');
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'window_surface' => $windowSurface,
            ]
        );

        \Event::dispatch(new StepDataHasBeenChangedEvent());
        $this->saveAdvices($request);
        // Save progress
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
}
