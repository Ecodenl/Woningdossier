<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\BuildingType;
use App\Models\EnergyLabel;
use App\Models\ExampleBuilding;
use App\Models\RoofType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeneralDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::all();
        $exampleBuildingTypes = ExampleBuilding::orderBy('order')->get();
        // TODO: when models are present, use them.
        $houseVentilations = [];
        $isInterested = ['jahoor', 'neehoor doe maar niet', 'kijk wel uit'];

        return view('cooperation.tool.general-data.index', compact('buildingTypes', 'roofTypes', 'energyLabels',
            'exampleBuildingTypes', 'houseVentilations', 'isInterested'));
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
    public function store(Request $request)
    {


        // Retrieve the name and basic data about the address
        $nameResident = $request->name_resident;
        $street = $request->street;
        $houseNumber = $request->house_number;
        $zipcode = $request->zip_code;
        $city = $request->residence;
        $email = $request->email;
        $phoneNumber = $request->phone_number;

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
        $interestedWindowsInLivingSpaces = $request->interested['window_in_living_space'];
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

        // TODO: Save the collected data

        dd($request->all());

        return redirect(back());
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
