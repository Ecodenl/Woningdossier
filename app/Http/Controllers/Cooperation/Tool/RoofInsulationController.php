<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\RoofInsulationCalculator;
use App\Http\Requests\RoofInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingRoofType;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RoofInsulationController extends Controller
{
    protected $step;

    public function __construct(Request $request) {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index()
    {

        // get the next page order
        $nextPage = $this->step->order + 1;

        // check if the user is interested in roof insulation, if not redirect to next step
        if (Auth::user()->isNotInterestedInStep('element', 5)) {

            $nextStep = Step::where('order', $nextPage)->first();

            return redirect(url('tool/'.$nextStep->slug));
        }

	    /** var Building $building */
	    $building = \Auth::user()->buildings()->first();

		/** var BuildingFeature $features */
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
//            'no' => [],
		];
		if ($currentRoofTypes instanceof Collection){
			/** var BuildingRoofType $currentRoofType */
			foreach($currentRoofTypes as $currentRoofType){
				$cat = $this->getRoofTypeCategory($currentRoofType->roofType);
				if (!empty($cat)) {
					$currentCategorizedRoofTypes[$cat] = $currentRoofType->toArray();
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
			    Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short', 'roof-insulation-flat-current')->first(),
			    Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short', 'roof-insulation-flat-replace-current')->first(),
		    ],
		    'pitched' => [
			    Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short', 'roof-insulation-pitched-inside')->first(),
			    Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short', 'roof-insulation-pitched-replace-tiles')->first(),
		    ],
	    ];
    }

	protected function saveAdvices(Request $request){
		/** var JsonResponse $results */
		$results = $this->calculate($request);
		$results = $results->getData(true);

		// Remove old results
		UserActionPlanAdvice::forMe()->forStep($this->step)->delete();

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

		foreach(array_keys($result) as $roofCat){
			$measureApplicationId = $request->input( 'building_roof_types.' . $roofCat . '.measure_application_id', 0 );
			if ( $measureApplicationId > 0 ) {
				// results in an advice
				$measureApplication = MeasureApplication::find( $measureApplicationId );
				if ( $measureApplication instanceof MeasureApplication ) {
					$actionPlanAdvice = null;
					// The measure type determines which array keys to take
					// as the replace array will always be present due to
					// how calculate() works in this step
					if($measureApplication->application == 'replace'){
						if (isset($results[$roofCat]['replace']['costs']) && $results[$roofCat]['replace']['costs'] > 0) {
							// take the replace array
							$actionPlanAdvice = new UserActionPlanAdvice( $results[ $roofCat ]['replace'] );
						}
					}
					else {
						if(isset($results[$roofCat]['cost_indication']) && $results[$roofCat]['cost_indication'] > 0){
							// take the array $roofCat array
							$actionPlanAdvice = new UserActionPlanAdvice( $results[ $roofCat ] );
							$actionPlanAdvice->costs = $results[$roofCat]['cost_indication'];
						}
					}

					if ($actionPlanAdvice instanceof UserActionPlanAdvice) {
						$actionPlanAdvice->user()->associate( Auth::user() );
						$actionPlanAdvice->measureApplication()->associate( $measureApplication );
						$actionPlanAdvice->step()->associate( $this->step );
						$actionPlanAdvice->save();
					}
				}

			}
			$extra = $request->input('building_roof_types.' . $roofCat . '.extra', []);
			if (array_key_exists('zinc_replaced_date', $extra)) {
				$zincReplaceYear = (int) $extra['zinc_replaced_date'];
				$surface = $request->input('building_roof_types.' . $roofCat . '.surface', 0);
				if ($zincReplaceYear > 0 && $surface > 0) {
					$zincReplaceMeasure = MeasureApplication::where('short', 'replace-zinc')->first();

					$year = RoofInsulationCalculator::determineApplicationYear($zincReplaceMeasure, $zincReplaceYear, 1);
					$costs = Calculator::calculateMeasureApplicationCosts( $zincReplaceMeasure, $surface, $year );

					$actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
					$actionPlanAdvice->user()->associate( Auth::user() );
					$actionPlanAdvice->measureApplication()->associate( $zincReplaceMeasure );
					$actionPlanAdvice->step()->associate( $this->step );
					$actionPlanAdvice->save();
				}
			}
			if (array_key_exists('tiles_condition', $extra)){
				$tilesCondition = (int) $extra['tiles_condition'];
				$surface = $request->input('building_roof_types.' . $roofCat . '.surface', 0);
				if ($tilesCondition > 0 && $surface > 0){
					$replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
					// no year here. Default is this year. It is incremented by factor * maintenance years
					$year = Carbon::now()->year;
					$roofTilesStatus = RoofTileStatus::find($tilesCondition);

					if ($roofTilesStatus instanceof RoofTileStatus){
						$factor = ($roofTilesStatus->calculate_value / 100);

						$year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
						$costs = Calculator::calculateMeasureApplicationCosts( $replaceMeasure, $surface, $year );

						$actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
						$actionPlanAdvice->user()->associate( Auth::user() );
						$actionPlanAdvice->measureApplication()->associate( $replaceMeasure );
						$actionPlanAdvice->step()->associate( $this->step );
						$actionPlanAdvice->save();
					}
				}
			}
			if (array_key_exists('bitumen_replaced_date', $extra)){
				$bitumenReplaceYear = (int) $extra['bitumen_replaced_date'];
				$surface = $request->input('building_roof_types.' . $roofCat . '.surface', 0);

				if ($bitumenReplaceYear > 0 && $surface > 0){
					$replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
					// no percentages here. We just do this to keep the determineApplicationYear definition in one place
					$year = $bitumenReplaceYear;
					$factor = 1;

					$year = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
					$costs = Calculator::calculateMeasureApplicationCosts( $replaceMeasure, $surface, $year );

					$actionPlanAdvice = new UserActionPlanAdvice(compact('costs', 'year'));
					$actionPlanAdvice->user()->associate( Auth::user() );
					$actionPlanAdvice->measureApplication()->associate( $replaceMeasure );
					$actionPlanAdvice->step()->associate( $this->step );
					$actionPlanAdvice->save();
				}

			}

		}

	}

    public function calculate(Request $request){
	    $result = [];
	    /**
	     * var Building $building
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
		$totalSurface = 0;

	    foreach(array_keys($result) as $cat){
		    $totalSurface += isset($roofTypes[$cat]['surface']) ? $roofTypes[$cat]['surface'] : 0;
	    }

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

		    $surface = $roofTypes[$cat]['surface'] ?? 0;
		    $heating = null;
		    // should take the bitumen field
		    $year = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? (int) $roofTypes[$cat]['extra']['bitumen_replaced_date'] : Carbon::now()->year;

		    // default, changes only for roof tiles effect
		    $factor = 1;

		    $advice = null;
		    $objAdvice = null;

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
					$catData['savings_gas'] = RoofInsulationCalculator::calculateGasSavings($building, $roofInsulationValue, $user->energyHabit, $heating, $surface, $totalSurface, $advice);
					$catData['savings_co2'] = Calculator::calculateCo2Savings($catData['savings_gas']);
					$catData['savings_money'] = round(Calculator::calculateMoneySavings($catData['savings_gas']));
					$catData['cost_indication'] = Calculator::calculateCostIndication($surface, $objAdvice->measure_name);
					$catData['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($catData['cost_indication'], $catData['savings_money']), 1);
					// The replace year is about the replacement of bitumen..
					$catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($objAdvice, $year, $factor);
				}
		    }

		    $tilesCondition = isset($roofTypes[$cat]['extra']['tiles_condition']) ? (int) $roofTypes[$cat]['extra']['tiles_condition'] : null;
		    if (!is_null($tilesCondition)){
			    $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
			    // no year here. Default is this year. It is incremented by factor * maintenance years
			    $year = Carbon::now()->year;
			    $roofTilesStatus = RoofTileStatus::find($tilesCondition);
			    if ($roofTilesStatus instanceof RoofTileStatus){
				    $factor = ($roofTilesStatus->calculate_value / 100);
			    }
		    }

		    $bitumenReplaceYear = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? (int) $roofTypes[$cat]['extra']['bitumen_replaced_date'] : null;
			if (!is_null($bitumenReplaceYear)){
				$replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
				// no percentages here. We just do this to keep the determineApplicationYear definition in one place
				$year = $bitumenReplaceYear;
			}


		    if (isset($replaceMeasure)){
			    $catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
			    $catData['replace']['cost'] = Calculator::calculateMeasureApplicationCosts( $replaceMeasure, $surface, $catData['replace']['year'] );
		    }

    		$result[$cat] = array_merge($result[$cat], $catData);
	    }

		return response()->json($result);

    }

    /**
     * Store a newly created resource in storage.
     *
     * param  \Illuminate\Http\Request  $request
     * return \Illuminate\Http\Response
     */
    public function store(RoofInsulationFormRequest $request)
    {

        // Get the user his building / house
        $building = Auth::user()->buildings()->first();
        // the selected roof types for the current situation
        $roofTypes = $request->input('building_roof_types', []);


        // remove the old answers
        if (BuildingRoofType::where('building_id', $building->id)->count() > 0) {
            BuildingRoofType::where('building_id', $building->id)->delete();
        }

        foreach($roofTypes as $i => $details){
            if (is_numeric($i) && is_numeric($details)){
                $roofType = RoofType::find($details);
                if ($roofType instanceof RoofType){
                    $cat = $this->getRoofTypeCategory($roofType);
                    // add as key to result array
                    $result[$cat] = [
                        'type' => $this->getRoofTypeSubCategory($roofType),
                    ];

                    $surface = isset($roofTypes[$cat]['surface']) ? $roofTypes[$cat]['surface'] : 0;
                    $elementValueId = isset($roofTypes[$cat]['element_value_id']) ? $roofTypes[$cat]['element_value_id'] : null;

                    $extraMeasureApplication = isset($roofTypes[$cat]['measure_application_id']) ? $roofTypes[$cat]['measure_application_id'] : "";
                    $extraBitumenReplacedDate = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? $roofTypes[$cat]['extra']['bitumen_replaced_date'] : "";
                    $extraZincReplacedDate = isset($roofTypes[$cat]['extra']['zinc_replaced_date']) ? $roofTypes[$cat]['extra']['zinc_replaced_date'] : "";
                    $extraTilesCondition = isset($roofTypes[$cat]['extra']['tiles_condition']) ? $roofTypes[$cat]['extra']['tiles_condition'] : "";

                    $buildingHeating = isset($roofTypes[$cat]['building_heating_id']) ? $roofTypes[$cat]['building_heating_id'] : null;
                    $comment = isset($roofTypes[$cat]['extra']['comment']) ? $roofTypes[$cat]['extra']['comment'] : null;

                    BuildingFeature::where('building_id', $building->id)->update([
                        'roof_type_id' => $request->input('building_features.roof_type_id')
                    ]);

                    // insert the new ones
                    BuildingRoofType::updateOrCreate(
                        [
                            'building_id' => $building->id,
                            'roof_type_id' => $roofType->id,
                        ],
                        [
                            'element_value_id' => $elementValueId,
                            'surface' => $surface,
                            'building_heating_id' => $buildingHeating,
                            'extra' => [
                                'measure_application_id' => $extraMeasureApplication,
                                'bitumen_replaced_date' => $extraBitumenReplacedDate,
                                'zinc_replaced_date' => $extraZincReplacedDate,
                                'tiles_condition' => $extraTilesCondition,
                                'comment' => $comment
                            ]
                        ]
                    );
                }
            }
        }


        // Save progress
	    $this->saveAdvices($request);
        \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.high-efficiency-boiler.index', ['cooperation' => $cooperation]);
    }

}
