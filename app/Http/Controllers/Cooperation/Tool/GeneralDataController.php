<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Requests\GeneralDataFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingType;
use App\Models\CentralHeatingAge;
use App\Models\ComfortLevelTapWater;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\PresentHeatPump;
use App\Models\PresentWindow;
use App\Models\Quality;
use App\Models\RoofType;
use App\Models\SolarWaterHeater;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Models\UserMotivation;
use App\Models\UserProgress;
use App\Models\Ventilation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class GeneralDataController extends Controller
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
        $building = \Auth::user()->buildings()->first();

        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();
        $exampleBuildingTypes = ExampleBuilding::orderBy('order')->get();
        $interests = Interest::orderBy('order')->get();
        $elements = Element::orderBy('order')->get();


        $insulations = PresentWindow::all();
        $houseVentilations = Ventilation::all();
        $qualities = Quality::all();
        $buildingHeatings = BuildingHeating::all();
        $solarWaterHeaters = SolarWaterHeater::all();
        $centralHeatingAges = CentralHeatingAge::all();
        $heatPumps = PresentHeatPump::all();
        $comfortLevelsTapWater = ComfortLevelTapWater::all();
        $motivations = Motivation::orderBy('order')->get();
        $energyHabit = Auth::user()->energyHabit;
        $steps = Step::orderBy('order')->get();




        return view('cooperation.tool.general-data.index', compact(
        	'building',
        	'buildingTypes', 'roofTypes', 'energyLabels',
            'exampleBuildingTypes', 'interests', 'elements',
	        'insulations','houseVentilations', 'qualities', 'buildingHeatings', 'solarWaterHeaters',
            'centralHeatingAges', 'heatPumps', 'comfortLevelsTapWater',
            'steps', 'motivations', 'energyHabit'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GeneralDataFormRequest $request)
    {
	    /** @var Building $building */
    	$building = Auth::user()->buildings()->first();
    	$features = $building->buildingFeatures;
    	if (!$features instanceof BuildingFeature){
    		$features = new BuildingFeature();
	    }
		$features->build_year = $request->get('build_year');
    	$features->surface = $request->get('surface');
    	$features->monument = $request->get('monument', 0);
    	$features->building_layers = $request->get('building_layers');

    	$energyLabel = EnergyLabel::find($request->get('energy_label_id'));
    	$features->energyLabel()->associate($energyLabel);

    	$buildingType = BuildingType::find($request->get('building_type_id'));
    	$features->buildingType()->associate($buildingType);

    	$roofType = RoofType::find($request->get('roof_type_id'));
    	$features->roofType()->associate($roofType);

    	$building->buildingFeatures()->save($features);

    	$elements = $request->get('element', []);
    	foreach($elements as $elementId => $elementValueId){
			$element = Element::find($elementId);
			$elementValue = ElementValue::find($elementValueId);
			if ($element instanceof Element && $elementValue instanceof ElementValue){
				$buildingElement = $building->buildingElements()->where('element_id', $element->id)->first();
				if (!$buildingElement instanceof BuildingElement){
					$buildingElement = new BuildingElement();
				}
				$buildingElement->elementValue()->associate($elementValue);
				$buildingElement->element()->associate($element);
				$buildingElement->building()->associate($building);
				$buildingElement->save();
			}
	    }


	    // Check if the user already has a motivation
	    if(UserMotivation::where('user_id', Auth::id())->count() > 0) {
    	    // if so drop the old ones
            UserMotivation::where('user_id', Auth::id())->delete();
        }
        // get the motivations
	    foreach ($request->get('motivation') as $key => $motivationId) {
    	    // Then save the UserMotivation
    	    $userMotivation = UserMotivation::create(
                [
    	            'user_id' => Auth::id(),
                    'motivation_id' => $motivationId,
                    'order' => $key
                ]
            );

    	}

	    $userEnegeryHabits = UserEnergyHabit::updateOrCreate(
	        [
	            'user_id' => Auth::id()
            ],
	        [
                'user_id' => Auth::id(),
                'resident_count' => $request->get('resident_count'),
                'thermostat_high' => $request->get('thermostat_high'),
                'thermostat_low' => $request->get('thermostat_low'),
                'hours_high' => $request->get('hours_high'),
                'heating_first_floor' => $request->get('heating_first_floor'),
                'heating_second_floor' => $request->get('heating_second_floor'),
                'cook_gas' => $request->get('cook_gas'),
                'water_comfort_id' => $request->get('water_comfort'),
                'amount_electricity' => $request->get('amount_electricity'),
                'amount_gas' => $request->get('amount_gas'),
                'amount_water' => $request->get('amount_water'),
                'living_situation_extra' => $request->get('living_situation_extra'),
                'motivation_extra' => $request->get('motivation_extra'),
            ]
        );



	    /*
        // Retrieve information about the building
        $buildingType = $request->building_type;
        $userSurface = $request->user_surface;
        $roofLayers = $request->roof_layers;
        $roofType = $request->roof_type;
        $isMonument = $request->is_monument == 1 ? 1 : 0;
        $buildingYear = $request->what_building_year;
        $currentEnergyLabel = $request->current_energy_label;

        // Retrieve values off the energy-saving-measures
        $facadeInsulation = $request->facade_insulation;
        $floorInsulation = $request->floor_insulation;
        $HrCvBoiler = $request->hr_cv_boiler;
        $sunPanels = $request->sun_panel;
        $monovalentHeatpump = $request->monovalent_heatpump;
        $houseVentilation = $request->house_ventilation;
        $windowsInLivingSpaces = $request->window_in_living_space;
        $windowsInSleepingSpaces = $request->window_in_sleeping_spaces;
        $roofIsolation = $request->roof_insulation;
        $hybridHeatpump = $request->hybrid_heatpump;
        $sunPanelPlacedDate = $request->sun_panel_placed_date;
        $sunBoiler = $request->sun_boiler;
        $houseVentilationPlacedDate = $request->house_ventilation_placed_date;
        // Retrieve the "interested in" from the above values ^
        $interestedFacadeInsulation = $request->interested['facade_insulation'];
        $interestedFloorInsulation = $request->interested['floor_insulation'];
        $interestedHrCvBoiler = $request->interested['hr_cv_boiler'];
        $interestedSunPanels = $request->interested['sun_panel'];
        $interestedMonovalentHeatpump = $request->interested['monovalent_heatpump'];
        $interestedHouseVentilation = $request->interested['house_ventilation'];
        $interestedWindowsInLivingSpaces = $request->interested['windows_in_living_space'];
        $interestedWindowsInSleepingSpaces = $request->interested['windows_in_sleeping_spaces'];
        $interestedRoofIsolation = $request->interested['roof_insulation'];
        $interestedHybridHeatpump = $request->interested['hybrid_heatpump'];
        $interestedSunBoiler = $request->interested['sun_boiler'];

        // Retrieve the info about the energy consumption in the building
        $totalCitizens = $request->total_citizens;
        $cookedOnGas = $request->cooked_on_gas;
        $thermostatHighest = $request->thermostat_highest;
        $thermostatLowest = $request->thermostat_lowest;
        $themostatMaxHourOnHighest = $request->max_hours_thermostat_highest;
        $situationFirstFloor = $request->situation_first_floor;
        $situationSecondFloor = $request->situation_second_floor;
        $comfortNiveauWarmTapWater = $request->comfortniveau_warm_tapwater;
        $pastYearElectricityUsage = $request->electricity_consumption_past_year;
        $pastYearGasUsage = $request->gas_usage_past_year;
*/
        // TODO: Save the collected data

	    // Save progress
	    \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.wall-insulation.index', ['cooperation' => $cooperation]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
