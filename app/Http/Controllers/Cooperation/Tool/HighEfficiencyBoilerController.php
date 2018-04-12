<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\NumberFormatter;
use App\Models\BoilerType;
use App\Models\Building;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HighEfficiencyBoilerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$user = \Auth::user();
	    /** @var Building $building */
    	$building = $user->buildings()->first();
    	$habit = $user->energyHabit;
	    $steps = Step::orderBy('order')->get();
	    // NOTE: building element hr-boiler tells us if it's there
	    $boiler = Service::where('short', 'boiler')->first();
		$boilerTypes = $boiler->values()->orderBy('order')->get();

		$installedBoiler = BuildingService::where('service_id', $boiler->id)->first();

        return view('cooperation.tool.hr-boiler.index', compact(
        	'habit', 'boiler', 'boilerTypes', 'installedBoiler',
	        'steps'));
    }

    public function calculate(Request $request){

    	$user = \Auth::user();

	    $result = [
		    'savings_gas' => 0,
		    'savings_co2' => 0,
		    'savings_money' => 0,
		    'cost_indication' => 0,
		    'interest_comparable' => 0,
	    ];

		$services = $request->input('building_services', []);
		// (there's only one..)
	    foreach($services as $serviceId => $options){
	    	$boilerService = Service::find($serviceId);

			if (array_key_exists('service_value_id', $options)){
				/** @var ServiceValue $boilerType */
				$boilerType = ServiceValue::where('service_id', $boilerService->id)
					->where('id', $options['service_value_id'])
					->first();

				$boilerEfficiency = $boilerType->keyFigureBoilerEfficiency;
				if ($boilerEfficiency->heating > 95){
					$result['boiler_advice'] = __('woningdossier.cooperation.tool.boiler.already-efficient');
				}

				if (array_key_exists('extra', $options)){
					$year = $options['extra'];

					$measure = MeasureApplication::translated('measure_name', 'Vervangen cv ketel', 'nl')->first(['measure_applications.*']);

					$result['savings_gas'] = HighEfficiencyBoilerCalculator::calculateGasSavings($boilerType, $user->energyHabit);
					$result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
					$result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
					//$result['cost_indication'] = Calculator::calculateCostIndication(1, $measure->measure_name);
					$result['replace_year'] = HighEfficiencyBoilerCalculator::determineApplicationYear($measure, $year);
					$result['cost_indication'] = Calculator::calculateMeasureApplicationCosts( $measure, 1, $result['replace_year'] );
					$result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
				}
			}
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
