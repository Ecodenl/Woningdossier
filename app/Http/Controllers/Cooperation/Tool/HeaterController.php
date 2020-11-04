<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Events\StepDataHasBeenChanged;
use App\Helpers\Cooperation\Tool\HeaterHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\HeaterFormRequest;
use App\Models\BuildingHeater;
use App\Models\ComfortLevelTapWater;
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

class HeaterController extends Controller
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

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $comfortLevels = ComfortLevelTapWater::orderBy('order')->get();
        $collectorOrientations = PvPanelOrientation::orderBy('order')->get();


        $energyHabitsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $buildingOwner->energyHabit()
        )->get();


        $heatersOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->heater()
        )->get();


        return view('cooperation.tool.heater.index', compact('building', 'buildingOwner',
            'collectorOrientations', 'typeIds', 'energyHabitsOrderedOnInputSourceCredibility', 'comfortLevels',
        'heatersOrderedOnInputSourceCredibility'
        ));
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $result = Heater::calculate($building, $user->energyHabit, $request->all());

        return response()->json($result);
    }

    /**
     * Store or update the existing record.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(HeaterFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $userInterests = $request->input('user_interests');
        UserInterestService::save($user, $inputSource, $userInterests['interested_in_type'], $userInterests['interested_in_id'], $userInterests['interest_id']);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        $saveData = $request->only('user_interests', 'building_heaters', 'user_energy_habits');
//        if (StepHelper::hasInterestInStep($user, Step::class, $this->step->id)) {
            HeaterHelper::save($building, $inputSource, $saveData);
//        } else {
//            HeaterHelper::clear($building, $inputSource);
//        }

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
