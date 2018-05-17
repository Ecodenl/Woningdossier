<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Http\Requests\HeaterFormRequest;
use App\Models\Building;
use App\Models\BuildingHeater;
use App\Models\ComfortLevelTapWater;
use App\Models\Cooperation;
use App\Models\HeaterComponentCost;
use App\Models\HeaterSpecification;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\MeasureApplication;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HeaterController extends Controller
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
    	$user = \Auth::user();
    	/** @var Building $building */
    	$building = $user->buildings()->first();
	    $steps = Step::orderBy('order')->get();

        $comfortLevels = ComfortLevelTapWater::orderBy('order')->get();
	    $collectorOrientations = PvPanelOrientation::orderBy('order')->get();
	    /** @var UserEnergyHabit|null $habits */
	    $habits = $user->energyHabit;
	    $currentComfort = null;
	    if ($habits instanceof UserEnergyHabit) {
		    $currentComfort = $habits->comfortLevelTapWater;
	    }
		$currentHeater = $building->heater;

        return view('cooperation.tool.heater.index', compact(
        	'comfortLevels', 'collectorOrientations',
	        'currentComfort', 'currentHeater', 'habits', 'steps'
        ));
    }

    public function calculate(Request $request){

	    $result = [
	    	'consumption' => [
	    		'water' => 0,
			    'gas' => 0,
		    ],
		    'specs' => [
		    	'size_boiler' => 0,
			    'size_collector' => 0,
		    ],
		    'production_heat' => 0,
		    'percentage_consumption' => 0,
		    'savings_gas' => 0,
		    'savings_co2' => 0,
		    'savings_money' => 0,
		    'cost_indication' => 0,
		    'interest_comparable' => 0,
	    ];

	    $comfortLevelId = $request->input('user_energy_habits.water_comfort_id', 0);
	    $comfortLevel = ComfortLevelTapWater::find($comfortLevelId);

	    $user = \Auth::user();
	    /** @var Building $building */
	    $building = $user->buildings()->first();
	    $habit = $user->energyHabit;

	    if ($habit instanceof UserEnergyHabit && $comfortLevel instanceof ComfortLevelTapWater) {
		    $consumption = KeyFigures::getCurrentConsumption($habit, $comfortLevel);
		    if ($consumption instanceof KeyFigureConsumptionTapWater){
		    	$result['consumption'] = [
		    		'water' => $consumption->water_consumption,
				    'gas' => $consumption->energy_consumption,
			    ];
		    }
		    $systemSpecs = KeyFigures::getSystemSpecifications($result['consumption']['water']);
		    if ($systemSpecs instanceof HeaterSpecification){
		    	$result['specs'] = [
		    		'size_boiler' => $systemSpecs->boiler,
				    'size_collector' => $systemSpecs->collector,
			    ];
		    	$result['production_heat'] = $systemSpecs->savings;
		    	$result['savings_gas'] = $result['production_heat'] / Kengetallen::gasKwhPerM3();
		    	$result['percentage_consumption'] = isset($result['consumption']['gas']) ? ($result['savings_gas'] / $result['consumption']['gas']) * 100 : 0;
			    $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
			    $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));

			    $componentCostBoiler = HeaterComponentCost::where('component', 'boiler')->where('size', $result['specs']['size_boiler'])->first();
			    $componentCostCollector = HeaterComponentCost::where('component', 'collector')->where('size', $result['specs']['size_collector'])->first();
			    $result['cost_indication'] = $componentCostBoiler->cost + $componentCostCollector->cost;

			    $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

			    $orientationId = $request->input('building_heaters.pv_panel_orientation_id', 0);
			    $angle = $request->input('building_heaters.angle', 0);
			    $orientation = PvPanelOrientation::find($orientationId);

			    $locationFactor = KeyFigures::getLocationFactor($building->postal_code);
			    $helpFactor = 0;
			    if ($orientation instanceof PvPanelOrientation && $angle > 0){
				    $yield = KeyFigures::getYield($orientation, $angle);
				    if ($yield instanceof PvPanelYield && $locationFactor instanceof PvPanelLocationFactor) {
					    $helpFactor = $yield->yield * $locationFactor->factor;
				    }
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


		    }
	    }


	    return response()->json($result);
    }


    /**
     * Store or update the existing record
     *
     * @param HeaterFormRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(HeaterFormRequest $request)
    {

        // Store the building heater part
        $buildingHeaters = $request->input('building_heaters', '');
        $pvPanelOrientation = isset($buildingHeaters['pv_panel_orientation_id']) ? $buildingHeaters['pv_panel_orientation_id'] : "";
        $angle = isset($buildingHeaters['angle']) ? $buildingHeaters['angle'] : "";

        BuildingHeater::updateOrCreate(
            [
                'building_id' => Auth::user()->buildings()->first()->id,
            ],
            [
                'pv_panel_orientation_id' => $pvPanelOrientation,
                'angle' => $angle,
            ]
        );

        // Update the habit
        $habits = $request->input('user_energy_habits', '');
        $waterComFortId = isset($habits['water_comfort_id']) ? $habits['water_comfort_id'] : "";

        UserEnergyHabit::where('user_id', Auth::id())->update([
            'water_comfort_id' => $waterComFortId,
        ]);

        // Save progress
	    $this->saveAdvices($request);
        Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]);
    }

	protected function saveAdvices(Request $request){
		/** @var JsonResponse $results */
		$results = $this->calculate($request);
		$results = $results->getData(true);

		// Remove old results
		UserActionPlanAdvice::forMe()->forStep($this->step)->delete();

		if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0){
			$measureApplication = MeasureApplication::where('short', 'heater-place-replace')->first();
			if ($measureApplication instanceof MeasureApplication){
				$actionPlanAdvice = new UserActionPlanAdvice($results);
				$actionPlanAdvice->costs = $results['cost_indication']; // only outlier
				$actionPlanAdvice->user()->associate(Auth::user());
				$actionPlanAdvice->measureApplication()->associate($measureApplication);
				$actionPlanAdvice->step()->associate($this->step);
				$actionPlanAdvice->save();
			}
		}

	}

}
