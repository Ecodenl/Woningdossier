<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SolarPanelFormRequest;
use App\Models\Building;
use App\Models\BuildingPvPanel;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; use App\Scopes\GetValueScope;

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

        $steps = Step::orderBy('order')->get();

        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $amountElectricity = ($user->energyHabit instanceof UserEnergyHabit) ? $user->energyHabit->amount_electricity : 0;

        $pvPanelOrientations = PvPanelOrientation::orderBy('order')->get();
        $buildingPvPanels = $building->pvPanels;
        $buildingPvPanelsForMe = $building->pvPanels()->forMe()->get();

        return view('cooperation.tool.solar-panels.index',
            compact('pvPanelOrientations', 'amountElectricity',
                'buildingPvPanels', 'steps', 'typeIds', 'buildingPvPanelsForMe'
            )
        );
    }

    public function calculate(Request $request)
    {
        $result = [
            'yield_electricity' => 0,
            'raise_own_consumption' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        $amountElectricity = $request->input('user_energy_habits.amount_electricity', 0);
        $peakPower = $request->input('building_pv_panels.peak_power', 0);
        $panels = $request->input('building_pv_panels.number', 0);
        $orientationId = $request->input('building_pv_panels.pv_panel_orientation_id', 0);
        $angle = $request->input('building_pv_panels.angle', 0);
        $orientation = PvPanelOrientation::find($orientationId);

        $locationFactor = KeyFigures::getLocationFactor($building->postal_code);
        $helpFactor = 0;
        if ($orientation instanceof PvPanelOrientation && $angle > 0) {
            $yield = KeyFigures::getYield($orientation, $angle);
            if ($yield instanceof PvPanelYield && $locationFactor instanceof PvPanelLocationFactor) {
                $helpFactor = $yield->yield * $locationFactor->factor;
            }
        }

        if ($peakPower > 0) {
            $number = ceil(($amountElectricity / KeyFigures::SOLAR_PANEL_ELECTRICITY_COST_FACTOR) / $peakPower);
            $result['advice'] = __('woningdossier.cooperation.tool.solar-panels.advice-text', ['number' => $number]);
            $wp = $panels * $peakPower;
            $result['total_power'] = __('woningdossier.cooperation.tool.solar-panels.total-power', ['wp' => $wp]);

            $result['yield_electricity'] = $wp * $helpFactor;

            $result['raise_own_consumption'] = $amountElectricity <= 0 ? 0 : ($result['yield_electricity'] / $amountElectricity) * 100;

            $result['savings_co2'] = $result['yield_electricity'] * Kengetallen::CO2_SAVINGS_ELECTRICITY;
            $result['savings_money'] = $result['yield_electricity'] * KeyFigures::COST_KWH;
            $result['cost_indication'] = $wp * KeyFigures::COST_WP;
            $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
        }

        if ($helpFactor >= 0.84) {
            $result['performance'] = [
                'alert' => 'success',
                'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal'),
            ];
        } elseif ($helpFactor < 0.70) {
            $result['performance'] = [
                'alert' => 'danger',
                'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go'),
            ];
        } else {
            $result['performance'] = [
                'alert' => 'warning',
                'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.possible'),
            ];
        }

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

        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'peak_power' => $peakPower,
                'number' => $number,
                'pv_panel_orientation_id' => $orientation,
                'angle' => $angle,
            ]
        );

        // Save progress
        $this->saveAdvices($request);
        $user->complete($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
    }

    protected function saveAdvices(Request $request)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;

        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->forStep($this->step)->delete();

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
