<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\WallInsulation;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\StepCommentService;
use App\Services\UserInterestService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WallInsulationController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeIds = [3];

        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        $facadeInsulation = $building->getBuildingElement('wall-insulation');
        $buildingFeature = $building->buildingFeatures;
        $buildingElements = $facadeInsulation->element;

        $buildingFeaturesForMe = BuildingFeature::withoutGlobalScope(GetValueScope::class)->forMe()->get();

        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        return view('cooperation.tool.wall-insulation.index', compact(
             'building', 'facadeInsulation',
            'surfaces', 'buildingFeature', 'interests', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe',
            'buildingElements'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $user = $building->user;

        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        // when its a step, and a user has no interest in it we will clear the data for that step
        // a user may had interest in the step and later on decided he has no interest, so we clear the data to prevent weird data in the dumps.
        if (StepHelper::hasInterestInStep($user, Step::class, $this->step->id)) {
            WallInsulationHelper::save($building, $inputSource, $request->validated());
        } else {
            WallInsulationHelper::clear($building, $inputSource);
        }

        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    public function calculate(WallInsulationRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $userEnergyHabit = $user->energyHabit;

        $result = WallInsulation::calculate($building, HoomdossierSession::getInputSource(true), $userEnergyHabit, $request->toArray());

        return response()->json($result);
    }
}
