<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\StepDataHasBeenChanged;
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
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
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
        $buildingPvPanels = $building->pvPanels;
        $buildingPvPanelsForMe = $building->pvPanels()->forMe()->get();
        $energyHabitsForMe = UserEnergyHabit::forMe()->get();

        return view('cooperation.tool.solar-panels.index',
            compact(
                'building', 'pvPanelOrientations', 'buildingOwner',
                'energyHabitsForMe', 'buildingPvPanels', 'typeIds',
                'buildingPvPanelsForMe'
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
     * @param SolarPanelFormRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SolarPanelFormRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $inputSourceId = HoomdossierSession::getInputSource();
        $user = $building->user;
        $buildingId = $building->id;

        $habit = $request->input('user_energy_habits', '');
        $habitAmountElectricity = isset($habit['amount_electricity']) ? $habit['amount_electricity'] : '0';

        $interests = $request->input('interest', '');
        UserInterest::saveUserInterests($user, $interests);

        $user->energyHabit()->withoutGlobalScope(GetValueScope::class)->update(['amount_electricity' => $habitAmountElectricity]);

        $pvPanels = $request->input('building_pv_panels', '');
        $peakPower = isset($pvPanels['peak_power']) ? $pvPanels['peak_power'] : '';
        $number = isset($pvPanels['number']) ? $pvPanels['number'] : '';
        $angle = isset($pvPanels['angle']) ? $pvPanels['angle'] : '';
        $orientation = isset($pvPanels['pv_panel_orientation_id']) ? $pvPanels['pv_panel_orientation_id'] : '';
        $comment = $request->get('comment', '');
        $comment = is_null($comment) ? '' : $comment;

        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'peak_power' => $peakPower,
                'number' => $number,
                'pv_panel_orientation_id' => $orientation,
                'comment' => $comment,
                'angle' => $angle,
            ]
        );

        // Save progress
        $this->saveAdvices($request);
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'solar-panels-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication'];
                $actionPlanAdvice->savings_electricity = $results['yield_electricity'];
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }
    }
}
