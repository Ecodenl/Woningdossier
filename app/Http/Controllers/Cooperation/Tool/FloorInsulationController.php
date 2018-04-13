<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\FloorInsulationCalculator;
use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Http\Requests\FloorInsulationFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class FloorInsulationController extends Controller
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
    	/** @var Building $building */
	    $building = \Auth::user()->buildings()->first();

	    $buildingInsulation = $building->getBuildingElement('floor-insulation');
		$floorInsulation = $buildingInsulation instanceof BuildingElement ? $buildingInsulation->element : null;

		$crawlspace = Element::where('short', 'crawlspace')->first();
		$buildingCrawlspace = $building->getBuildingElement($crawlspace->short);

		$crawlspacePresent = 2; // unknown
		if ($buildingCrawlspace instanceof \App\Models\BuildingElement){
			if ($buildingCrawlspace->elementValue instanceof \App\Models\ElementValue){
				$crawlspacePresent = 0; // yes
			}
		}
		else {
			$crawlspacePresent = 1; // now
		}

		//$crawlspaceAccessOptions = CrawlspaceAccess::all();


	    $buildingFeatures = $building->buildingFeatures;
        $steps = Step::orderBy('order')->get();

        return view('cooperation.tool.floor-insulation.index', compact(
            'floorInsulation', 'buildingInsulation',
            'crawlspace', 'buildingCrawlspace',
            'crawlspacePresent', 'steps', 'buildingFeatures'
        ));
    }

    public function calculate(Request $request){
	    /**
	     * @var Building $building
	     */
	    $user     = \Auth::user();
	    $building = $user->buildings()->first();

	    $result = [
		    'savings_gas' => 0,
		    'savings_co2' => 0,
		    'savings_money' => 0,
		    'cost_indication' => 0,
	    ];

	    $crawlspace = Element::where('short', 'crawlspace')->first();

	    $elements = $request->get('element', []);
	    $buildingElements = $request->get('building_elements', []);
	    $buildingFeatures = $request->get('building_features', []);

	    $surface = array_key_exists('surface', $buildingFeatures) ? $buildingFeatures['surface'] : 0;

	    $crawlspaceValue = null;
	    if (array_key_exists($crawlspace->id, $buildingElements)){
	    	if (array_key_exists('element_value_id', $buildingElements[$crawlspace->id])){
	    		$crawlspaceValue = ElementValue::where('element_id', $crawlspace->id)
				    ->where('id', $buildingElements[$crawlspace->id]['element_value_id'])
				    ->first();
		    }
		    if (array_key_exists('extra', $buildingElements[$crawlspace->id])){
	    		// Check if crawlspace is accessible. If not: show warning!
	    		if ($buildingElements[$crawlspace->id]['extra'] == "no") {
				    $result['crawlspace_access'] = "warning";
			    }
		    }
	    }
	    else {
	    	// first page request
		    $crawlspaceValue = $crawlspace->values()->orderBy('order')->first();
	    }

	    if ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 45){
		    $advice = Temperature::FLOOR_INSULATION_FLOOR;
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.floor-insulation.insulation-advice.floor');
	    }
	    elseif ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 30){
		    $advice = Temperature::FLOOR_INSULATION_BOTTOM;
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.floor-insulation.insulation-advice.bottom');
	    }
	    else {
		    $advice = Temperature::FLOOR_INSULATION_RESEARCH;
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.floor-insulation.insulation-advice.research');
	    }

	    $floorInsulation = Element::where('short', 'floor-insulation')->first();
	    if (array_key_exists($floorInsulation->id, $elements)){
	    	$floorInsulationValue = ElementValue::where('element_id', $floorInsulation->id)->where('id', $elements[$floorInsulation->id])->first();
			if ($floorInsulationValue instanceof ElementValue){
				$result['savings_gas'] = FloorInsulationCalculator::calculateGasSavings($building, $floorInsulationValue, $user->energyHabit, $surface, $advice);
			}

		    $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
		    $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
		    $result['cost_indication'] = Calculator::calculateCostIndication($surface, $result['insulation_advice']);
		    $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
	    }




	    return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FloorInsulationFormRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FloorInsulationFormRequest $request)
    {
    	// Get the value's from the input's
        $floorInsulation = $request->floor_insulation;
        $hasCrawlspace = $request->has_crawlspace;
        $hasCrawlspaceAccess = $request->crawlspace_access;
        $crawlspaceHeight = $request->crawlspace_height;
        $floorSurface = $request->floor_surface;

        // TODO: store the request
        return redirect()->route('cooperation.tool.roof-insulation.index', ['cooperation' => App::make('Cooperation')]);
    }

}
