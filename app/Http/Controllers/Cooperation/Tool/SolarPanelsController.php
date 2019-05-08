<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\StepDataHasBeenChangedEvent;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Helpers\Translation;
use App\Http\Controllers\Controller;
use App\Http\Requests\SolarPanelFormRequest;
use App\Models\Building;
use App\Models\BuildingPvPanel;
use App\Models\Cooperation;
use App\Models\Interest;
use App\Models\LanguageLine;
use App\Models\MeasureApplication;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolarPanelsController extends Controller
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
        $typeIds = [7];

        $building = Building::find(HoomdossierSession::getBuilding());
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
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $result = SolarPanel::calculate($building, $user, $request->all());

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
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

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
        $building->complete($this->step);
        ($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        $nextStep = StepHelper::getNextStep($this->step);
        $url = route($nextStep['route'], ['cooperation' => $cooperation]);

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        \Event::dispatch(new StepDataHasBeenChangedEvent());
        return redirect($url);
    }

    protected function saveAdvices(Request $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
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
