<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralDataFormRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingService;
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
use App\Models\RoofType;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\SolarWaterHeater;
use App\Models\Step;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Models\UserMotivation;
use App\Models\Ventilation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralDataController extends Controller
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
        $building = \Auth::user()->buildings()->first();

        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();
        $exampleBuildings = ExampleBuilding::forMyCooperation()->orderBy('order')->get();
        $interests = Interest::orderBy('order')->get();
        $elements = Element::whereIn('short', [
            'sleeping-rooms-windows', 'living-rooms-windows',
            'wall-insulation', 'floor-insulation', 'roof-insulation',
            ])->orderBy('order')->get();
        $services = Service::orderBy('order')->get();

        $insulations = PresentWindow::all();
        $houseVentilations = Ventilation::all();
        $buildingHeatings = BuildingHeating::all();
        $solarWaterHeaters = SolarWaterHeater::all();
        $centralHeatingAges = CentralHeatingAge::all();
        $heatPumps = PresentHeatPump::all();
        $comfortLevelsTapWater = ComfortLevelTapWater::all();
        $motivations = Motivation::orderBy('order')->get();
        $energyHabit = Auth::user()->energyHabit;
        $steps = Step::orderBy('order')->get();
        $step = $this->step;

        return view('cooperation.tool.general-data.index', compact(
            'building', 'step',
            'buildingTypes', 'roofTypes', 'energyLabels',
            'exampleBuildings', 'interests', 'elements',
            'insulations', 'houseVentilations', 'buildingHeatings', 'solarWaterHeaters',
            'centralHeatingAges', 'heatPumps', 'comfortLevelsTapWater',
            'steps', 'motivations', 'energyHabit', 'services'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(GeneralDataFormRequest $request)
    {
        /** @var Building $building */
        $building = Auth::user()->buildings()->first();

        $exampleBuildingId = $request->get('example_building_id', null);
        if (! is_null($exampleBuildingId)) {
            $exampleBuilding = ExampleBuilding::forMyCooperation()->where('id',
                $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        }

        $features = $building->buildingFeatures;
        if (! $features instanceof BuildingFeature) {
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

        // the user has not an option / dropdown to set an interest foor the living room windows
        // we will always set the interest level to 1 so the user can still go to the step.
        $livingRoomWindowsElement = Element::where('short', "living-rooms-windows")->first();
        $yesOnShortTermInterest = Interest::where('calculate_value', 1)->first();
        UserInterest::updateOrCreate(
            [
                'user_id'            => Auth::id(),
                'interested_in_type' => 'element',
                'interested_in_id'   => $livingRoomWindowsElement->id,
            ],
            [
                'user_id'            => Auth::id(),
                'interested_in_type' => 'element',
                'interested_in_id'   => $livingRoomWindowsElement->id,
                'interest_id'        => $yesOnShortTermInterest->id,
            ]
        );

        foreach ($elements as $elementId => $elementValueId) {
            $element = Element::find($elementId);
            $elementValue = ElementValue::find($elementValueId);

            // Get the interest field off the element
            $elementInterestId = $request->input('user_interest.element.'.$elementId.'', '');

            if ($element instanceof Element && $elementValue instanceof ElementValue) {
                $buildingElement = $building->buildingElements()->where('element_id', $element->id)->first();
                if (! $buildingElement instanceof BuildingElement) {
                    $buildingElement = new BuildingElement();
                }
                $buildingElement->elementValue()->associate($elementValue);
                $buildingElement->element()->associate($element);
                $buildingElement->building()->associate($building);
                $buildingElement->save();

                if (! empty($elementInterestId)) {
                    // We'll create the user interests for the elements or update it
                    UserInterest::updateOrCreate(
                        [
                            'user_id'            => Auth::id(),
                            'interested_in_type' => 'element',
                            'interested_in_id'   => $elementId,
                        ],
                        [
                            'user_id'            => Auth::id(),
                            'interested_in_type' => 'element',
                            'interested_in_id'   => $elementId,
                            'interest_id'        => $elementInterestId,
                        ]
                    );
                }
            }
        }

        // save the services
        $services = $request->get('service', []);
        foreach ($services as $serviceId => $serviceValueId) {
            // get the service based on the service id from the form
            $service = Service::find($serviceId);
            //get the service values
            $serviceValue = ServiceValue::find($serviceValueId);

            // get the extra fields (date)
            $serviceExtra = $request->input($serviceId.'.extra', []);

            // Get the interest field off the service
            $serviceInterestId = $request->input('user_interest.service.'.$serviceId.'', '');

            if ($service instanceof Service) {
                // get a building service
                $buildingService = $building->buildingServices()->where('service_id', $service->id)->first();

                if (! $buildingService instanceof BuildingService) {
                    $buildingService = new BuildingService();
                }
                // check if the current service is a sun panel
                // if so, we will need to put the value / valueId inside the extra field.
                if ('total-sun-panels' == $service->short) {
                    //$buildingService->extra = ['value' => $serviceValueId, 'date' => $serviceExtra];
                    $serviceExtra['value'] = $serviceValueId;
                    $buildingService->extra = array_only($serviceExtra, ['value', 'year']);
                }
                // if its a ventilation, is has a dropdown so it has a serviceValue
                elseif ('house-ventilation' == $service->short) {
                    $buildingService->extra = array_only($serviceExtra, ['year']);
                    $buildingService->serviceValue()->associate($serviceValue);
                } else {
                    $buildingService->serviceValue()->associate($serviceValue);
                }

                $buildingService->service()->associate($service);
                $buildingService->building()->associate($building);
                $buildingService->save();

                // We'll create the user interests for the services or update it
                if (! empty($serviceInterestId)) {
                    UserInterest::updateOrCreate(
                        [
                            'user_id'            => Auth::id(),
                            'interested_in_type' => 'service',
                            'interested_in_id'   => $serviceId,
                        ],
                        [
                            'user_id'            => Auth::id(),
                            'interested_in_type' => 'service',
                            'interested_in_id'   => $serviceId,
                            'interest_id'        => $serviceInterestId,
                        ]
                    );
                }
            }
        }

        // Check if the user already has a motivation
        if (UserMotivation::where('user_id', Auth::id())->count() > 0) {
            // if so drop the old ones
            UserMotivation::where('user_id', Auth::id())->delete();
        }
        // get the motivations
        foreach ($request->get('motivation', []) as $key => $motivationId) {
            // Then save the UserMotivation
            UserMotivation::create(
                [
                    'user_id' => Auth::id(),
                    'motivation_id' => $motivationId,
                    'order' => $key,
                ]
            );
        }

        UserEnergyHabit::updateOrCreate(
            [
                'user_id' => Auth::id(),
            ],
            [
                'user_id' => Auth::id(),
                'resident_count' => $request->get('resident_count'),
                'thermostat_high' => $request->get('thermostat_high', 20),
                'thermostat_low' => $request->get('thermostat_low', 15),
                'hours_high' => $request->get('hours_high', 12),
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

        // Save progress
        \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));

        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
    }
}
