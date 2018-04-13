<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SolarPanelsController extends Controller
{
    protected $step;

    public function __construct(Request $request) {
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
	    $steps = Step::orderBy('order')->get();
        $user = \Auth::user();
	    /**
	     * @var Building $building
	     */
        $building = $user->buildings()->first();
        $user->energyHabit;
        $amountElectricity = ($user->energyHabit instanceof UserEnergyHabit) ? $user->energyHabit->amount_electricity : 0;

		$pvPanelOrientations = PvPanelOrientation::orderBy('order')->get();
		$buildingPvPanels = $building->pvPanels;


	    return view('cooperation.tool.solar-panels.index',
		    compact(
		    	'pvPanelOrientations', 'amountElectricity',
			    'buildingPvPanels', 'steps'
		    )
	    );
    }

    public function calculate(Request $request){

    	$result = [
    		'yield_electricity' => 0,
		    'raise_own_consumption' => 0,
		    'savings_co2' => 0,
		    'savings_money' => 0,
		    'cost_indication' => 0,
		    'interest_comparable' => 0,
	    ];

		$user = \Auth::user();
		$building = $user->buildings()->first();

		$amountElectricity = $request->input('user_energy_habits.amount_electricity', 0);
		$peakPower = $request->input('building_pv_panels.peak_power', 0);
		$panels = $request->input('building_pv_panels.number', 0);
		$orientationId = $request->input('building_pv_panels.pv_panel_orientation_id', 0);
	    $angle = $request->input('building_pv_panels.angle', 0);
	    $orientation = PvPanelOrientation::find($orientationId);

	    $locationFactor = KeyFigures::getLocationFactor($building->postal_code);
	    $helpFactor = 0;
	    if ($orientation instanceof PvPanelOrientation && $angle > 0){
		    $yield = KeyFigures::getYield($orientation, $angle);
		    if ($yield instanceof PvPanelYield && $locationFactor instanceof PvPanelLocationFactor) {
			    $helpFactor = $yield->yield * $locationFactor->factor;
		    }
	    }

		if ($peakPower > 0){
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

		if ($helpFactor >= 0.84){
	    	$result['performance'] = [
	    		'alert' => 'success',
			    'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal'),
		    ];
		}
		elseif($helpFactor < 0.70){
	    	$result['performance'] = [
	    		'alert' => 'danger',
			    'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go'),
		    ];
		}
		else {
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
}
