<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\InsulatedGlazing;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\InsulatedGlazingHelper;
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
use App\Services\DumpService;
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

                if (!$currentInsulatedGlazingInputs->isEmpty()) {
                    $buildingInsulatedGlazingsForMe[$measureApplication->id] = $currentInsulatedGlazingInputs;
                }
                if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                    $buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
                }


                // get interests for the measure
                $measureInterestId = Hoomdossier::getMostCredibleValue(
                    $buildingOwner->userInterestsForSpecificType(MeasureApplication::class, $measureApplication->id), 'interest_id'
                );

                // when there is no interest found and the short is for hrpp glass, then we will set the interest given for the step insulated glazing.
                if (is_null($measureInterestId) && in_array($measureApplicationShort, ['hrpp-glass-only'])) {
                    $measureInterestId = Hoomdossier::getMostCredibleValue(
                        $buildingOwner->userInterestsForSpecificType(Step::class, $this->step->id), 'interest_id'
                    );
                }

                $userInterests[$measureApplication->id] = $measureInterestId;

                $measureApplications[] = $measureApplication;
            }
        }

        $myBuildingElements = BuildingElement::forMe()->get();
        $userInterestsForMe = UserInterest::forMe()->where('interested_in_type', MeasureApplication::class)->get();

        return view('cooperation.tool.insulated-glazing.index', compact(
            'building', 'interests', 'myBuildingElements', 'buildingOwner', 'userInterestsForMe',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
            'userInterests', 'crackSealing', 'frames', 'woodElements', 'buildingFeaturesForMe',
            'paintworkStatuses', 'woodRotStatuses', 'buildingInsulatedGlazingsForMe'
        ));
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InsulatedGlazingFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $userInterests = $request->input('user_interests');
        $interests = collect();
        foreach ($userInterests as $interestInId => $userInterest) {
            // so we can determine the highest interest level later on.
            $interests->push(Interest::find($userInterest['interest_id']));
            UserInterestService::save($user, $inputSource, $userInterest['interested_in_type'], $interestInId, $userInterest['interest_id']);
        }

        // get the highest interest level (which is the lowst calculate value.)
        $highestInterestLevelInterestId = $interests->unique('id')->min('calculate_value');
        // we have to update the step interest based on the interest for the measure application.
        UserInterestService::save($user, $inputSource, Step::class, Step::findByShort('insulated-glazing')->id, $highestInterestLevelInterestId);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        // save the step data
        $saveData = $request->only('user_interests', 'building_insulated_glazings', 'building_features', 'building_elements', 'building_paintwork_statuses');
        InsulatedGlazingHelper::save($building, $inputSource, $saveData);

        // Save progress
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (!empty($nextStep['tab_id'])) {
            $url .= '#' . $nextStep['tab_id'];
        }

        return redirect($url);
    }
}
