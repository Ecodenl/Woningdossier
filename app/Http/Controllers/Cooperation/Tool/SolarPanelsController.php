<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\SolarPanelHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\SolarPanelFormRequest;
use App\Models\BuildingPvPanel;
use App\Models\MeasureApplication;
use App\Models\PvPanelOrientation;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Services\StepCommentService;
use App\Services\UserInterestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolarPanelsController extends Controller
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
        $typeIds = [7];

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $pvPanelOrientations = PvPanelOrientation::orderBy('order')->get();

        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();

        $pvPanelsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->pvPanels()
        )->get();

        return view('cooperation.tool.solar-panels.index',
            compact(
                'building', 'pvPanelOrientations', 'buildingOwner', 'typeIds',
                'energyHabitsOrderedOnInputSourceCredibility', 'pvPanelsOrderedOnInputSourceCredibility'
            )
        );
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $result = SolarPanel::calculate($building, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SolarPanelFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $saveData = $request->only('building_pv_panels', 'user_energy_habits', 'user_interests');

//        if (StepHelper::hasInterestInStep($user, Step::class, $this->step->id)) {
            SolarPanelHelper::save($building, $inputSource, $saveData);
//        } else {
//            SolarPanelHelper::clear($building, $inputSource);
//        }

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
