<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\InsulatedGlazing;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Models\WoodRotStatus;
use App\Scopes\GetValueScope;
use App\Services\ModelService;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsulatedGlazingController extends Controller
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
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @var Building
         */
        $building = HoomdossierSession::getBuilding(true);
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

        $buildingFeaturesForMe = $building->buildingFeatures()->forMe()->get();
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
                $measureInterestId = Hoomdossier::getMostCredibleValue(
                    $buildingOwner->userInterestsForSpecificType(MeasureApplication::class, $measureApplication->id), 'interest_id'
                );

                $userInterests[$measureApplication->id] = $measureInterestId;

                $measureApplications[] = $measureApplication;
            }
        }

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
        $user = HoomdossierSession::getBuilding(true)->user;
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
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $result = InsulatedGlazing::calculate($building, $inputSource, $building->user->energyHabit, $request->all());

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
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = $inputSource->id;

        $userInterests = $request->input('user_interests');
        foreach ($userInterests as $interestInId => $userInterest) {
            UserInterestService::save($user, $inputSource, $userInterest['interested_in_type'], $interestInId, $userInterest['interest_id']);
        }

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);


        $buildingInsulatedGlazings = $request->input('building_insulated_glazings', '');

        // Saving the insulate glazings
        $interests = collect();
        foreach ($buildingInsulatedGlazings as $measureApplicationId => $buildingInsulatedGlazing) {

            $insulatedGlazingId = $buildingInsulatedGlazing['insulated_glazing_id'];
            $buildingHeatingId = $buildingInsulatedGlazing['building_heating_id'];
            $m2 = isset($buildingInsulatedGlazing['m2']) ? $buildingInsulatedGlazing['m2'] : 0;
            $windows = isset($buildingInsulatedGlazing['windows']) ? $buildingInsulatedGlazing['windows'] : 0;

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
                ]
            );
        }

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

        $lastPaintedYear = null;
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
                'wood_rot_status_id' => $paintWorkStatuses['wood_rot_status_id'],
            ]
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            $request->input('building_features')
        );

        $this->saveAdvices($request);
        // Save progress
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
