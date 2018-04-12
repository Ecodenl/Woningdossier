<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\RoofInsulationCalculator;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingRoofType;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class RoofInsulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    /** @var Building $building */
	    $building = \Auth::user()->buildings()->first();

		/** @var BuildingFeature $features */
	    $features = $building->buildingFeatures;
	    $roofTypes = RoofType::all();
	    $steps = Step::orderBy('order')->get();
	    $currentRoofTypes = $building->roofTypes;
	    $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
	    $roofInsulation = Element::where('short', 'roof-insulation')->first();
	    $heatings = BuildingHeating::all();
	    $measureApplications = $this->getMeasureApplicationsAdviceMap();

		$currentCategorizedRoofTypes = [
			'flat' => [],
			'pitched' => [],
		];
		if ($currentRoofTypes instanceof Collection){
			/** @var BuildingRoofType $currentRoofType */
			foreach($currentRoofTypes as $currentRoofType){
				$cat = $this->getRoofTypeCategory($currentRoofType->roofType);
				if (!empty($cat)) {
					$currentCategorizedRoofTypes[] = $currentRoofType->toArray();
				}
			}
		}

        return view('cooperation.tool.roof-insulation.index', compact(
        	'features', 'roofTypes', 'steps',
        	 'currentRoofTypes', 'roofTileStatuses', 'roofInsulation',
	         'heatings', 'measureApplications', 'currentCategorizedRoofTypes'));
    }

    protected function getRoofTypeCategory(RoofType $roofType){
	    if ($roofType->calculate_value <= 2){
	    	return 'pitched';
	    }
	    if ($roofType->calculate_value <= 4){
	    	return 'flat';
	    }
	    return '';
    }

    protected function getRoofTypeSubCategory(RoofType $roofType){
    	if ($roofType->calculate_value == 1){
    		return 'tiles';
	    }
	    if ($roofType->calculate_value == 2){
    		return 'bitumen';
	    }
	    if ($roofType->calculate_value == 4){
    		return 'zinc';
	    }
	    return '';
    }

    protected function getMeasureApplicationsAdviceMap(){
	    return [
		    'flat' => [
			    Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::translated('measure_name', 'Isolatie plat dak op huidige dakbedekking', 'nl')->first(['measure_applications.*']),
			    Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::translated('measure_name', 'Isolatie plat dak met vervanging van de dakbedekking', 'nl')->first(['measure_applications.*']),
		    ],
		    'pitched' => [
			    Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::translated('measure_name', 'Isolatie hellend dak van binnen uit', 'nl')->first(['measure_applications.*']),
			    Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::translated('measure_name', 'Isolatie hellend dak met vervanging van de dakpannen', 'nl')->first(['measure_applications.*']),
		    ],
	    ];
    }

    public function calculate(Request $request){
	    $result = [];
	    /**
	     * @var Building $building
	     */
	    $user     = \Auth::user();
	    $building = $user->buildings()->first();

    	$roofTypes = $request->input('building_roof_types', []);
    	foreach($roofTypes as $i => $details){
    		if (is_numeric($i) && is_numeric($details)){
    			$roofType = RoofType::find($details);
    			if ($roofType instanceof RoofType){
    				$cat = $this->getRoofTypeCategory($roofType);
    				// add as key to result array
				    $result[$cat] = [
				    	'type' => $this->getRoofTypeSubCategory($roofType),
				    ];
			    }
		    }
	    }

	    $roofInsulation = Element::where('short', 'roof-insulation')->first();
    	$adviceMap = $this->getMeasureApplicationsAdviceMap();

	    foreach(array_keys($result) as $cat){

			// defaults
		    $catData = [
			    'savings_gas' => 0,
			    'savings_co2' => 0,
			    'savings_money' => 0,
			    'cost_indication' => 0,
			    'interest_comparable' => 0,
			    'replace' => [
			    	'cost' => 0,
			        'year' => null,
			    ],
		    ];

		    $surface = isset($roofTypes[$cat]['surface']) ? $roofTypes[$cat]['surface'] : 0;
		    $heating = null;
		    //$year = isset($roofTypes[$cat]['extra']) ?
		    if (isset($roofTypes[$cat]['building_heating_id'])){
		    	$heating = BuildingHeating::find($roofTypes[$cat]['building_heating_id']);
		    }
		    if (isset($roofTypes[$cat]['measure_application_id'])){
				$measureAdvices = $adviceMap[$cat];
				foreach($measureAdvices as $strAdvice => $measureAdvice){
					if($roofTypes[$cat]['measure_application_id'] == $measureAdvice->id){
						$advice = $strAdvice;
						// we do this as we don't want the advice to be in
						// $result['insulation_advice'] as in other calculating
						// controllers
						$objAdvice = $measureAdvice;
					}
				}

		    }

	    	if (isset($roofTypes[$cat]['element_value_id'])) {
				$roofInsulationValue = ElementValue::where('element_id', $roofInsulation->id)->where('id', $roofTypes[$cat]['element_value_id'])->first();

				if ($roofInsulationValue instanceof ElementValue && $heating instanceof BuildingHeating && isset($advice)){
					$catData['savings_gas'] = RoofInsulationCalculator::calculateGasSavings($building, $roofInsulationValue, $user->energyHabit, $heating, $surface, $advice);
					$catData['savings_co2'] = Calculator::calculateCo2Savings($catData['savings_gas']);
					$catData['savings_money'] = round(Calculator::calculateMoneySavings($catData['savings_gas']));
					$catData['cost_indication'] = Calculator::calculateCostIndication($surface, $objAdvice->measure_name);
					$catData['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($catData['cost_indication'], $catData['savings_money']), 1);
				}

		    }


    		$result[$cat] = array_merge($result[$cat], $catData);
	    }


		//dd($request->input('building_roof_types', []));


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
