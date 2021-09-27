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
use App\Models\ComfortLevelTapWater;
use App\Models\PvPanelOrientation;
use App\Models\Step;
use App\Services\ConsiderableService;
use App\Services\StepCommentService;
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

        ConsiderableService::save($this->step, $user, $inputSource, $request->validated()['considerables'][$this->step->id]);

        $stepComments = $request->input('step_comments');
        StepCommentService::save($building, $inputSource, $this->step, $stepComments['comment']);

        (new HeaterHelper($user, $inputSource))
            ->setValues($request->only('building_heaters', 'user_energy_habits', 'considerables'))
            ->saveValues()
            ->createAdvices();

        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        $building->update([
            'has_answered_expert_question' => true,
        ]);
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
