<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
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
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Motivation;
use App\Models\PresentHeatPump;
use App\Models\PresentWindow;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\SolarWaterHeater;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Models\UserMotivation;
use App\Models\Ventilation;
use App\Scopes\GetValueScope;
use App\Services\ExampleBuildingService;
use App\Services\ModelService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class GeneralDataController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace(['tool', '/'], '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation)
    {
//        dd($step, $subStep);

//        return view('cooperation.tool.general-data.building-characteristics.index');
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        $buildingTypes = BuildingType::all();
        $roofTypes = RoofType::all();
        $energyLabels = EnergyLabel::where('country_code', 'nl')->get();

        //$exampleBuildings = ExampleBuilding::forAnyOrMyCooperation()->orderBy('order')->get();
        $buildingType = $building->getBuildingType(HoomdossierSession::getInputSource(true));
        $exampleBuildings = collect([]);
        if ($buildingType instanceof BuildingType) {
            $exampleBuildings = ExampleBuilding::forMyCooperation()
                ->where('building_type_id', '=', $buildingType->id)
                ->get();
        }

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
        $energyHabit = $buildingOwner->energyHabit;

        // Get possible remarks from the coach on energy habits
        $coachSource = InputSource::findByShort('coach');
        $coachEnergyHabitRemarks = UserEnergyHabit::withoutGlobalScope(GetValueScope::class)
                                       ->where('user_id', $buildingOwner->id)
                                       ->where('input_source_id', $coachSource->id)
                                       ->first();
        $step = $this->step;
        $userEnergyHabitsForMe = UserEnergyHabit::forMe()->get();
        $userInterestsForMe = UserInterest::forMe()->get();

        return view('cooperation.tool.general-data.index', compact(
            'building', 'step',
            'coachEnergyHabitRemarks', 'userInterestsForMe',
            'buildingTypes', 'roofTypes', 'energyLabels',
            'exampleBuildings', 'interests', 'elements', 'userEnergyHabitsForMe',
            'insulations', 'houseVentilations', 'buildingHeatings', 'solarWaterHeaters',
            'centralHeatingAges', 'heatPumps', 'comfortLevelsTapWater',
             'motivations', 'energyHabit', 'services'
        ));
    }

    public function applyExampleBuilding(Request $request)
    {
        $exampleBuildingId = $request->get('example_building_id', null);

        $building = HoomdossierSession::getBuilding(true);
        $buildYear = $building->getBuildYear();

        // There is one strange option: "Er is geen passende voorbeeldwoning"
        if (is_null($exampleBuildingId) && ! is_null($buildYear)) {
            // No fitting? Try to set the generic.
            $btype = $building->getBuildingType(HoomdossierSession::getInputSource(true));
            if ($btype instanceof BuildingType) {
                $generic = ExampleBuilding::generic()->where('building_type_id', $btype->id)->first();
                if ($generic instanceof ExampleBuilding) {
                    $exampleBuildingId = $generic->id;
                }
            }
            $building->example_building_id = null;
            $building->save();
        }

        if (! is_null($exampleBuildingId) && ! is_null($buildYear)) {
            $exampleBuilding = ExampleBuilding::forAnyOrMyCooperation()->where('id', $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
                ExampleBuildingService::apply($exampleBuilding, $buildYear, $building);

                return response()->json();
            }
        }
        // Something went wrong!
        return response()->json([], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FormRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(GeneralDataFormRequest $request)
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $exampleBuildingId = $request->get('example_building_id', null);
        if (! is_null($exampleBuildingId)) {
            $exampleBuilding = ExampleBuilding::forMyCooperation()->where('id',
                $exampleBuildingId)->first();
            if ($exampleBuilding instanceof ExampleBuilding) {
                $building->exampleBuilding()->associate($exampleBuilding);
                $building->save();
            }
        }

        $surface = NumberFormatter::reverseFormat($request->get('surface', 0));

        $features = BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'surface' => $surface,
                'monument' => $request->get('monument'),
                'building_layers' => $request->get('building_layers'),
            ]
        );

        $energyLabel = EnergyLabel::find($request->get('energy_label_id'));
        $features->energyLabel()->associate($energyLabel);

        $roofType = RoofType::find($request->get('roof_type_id'));
        $features->roofType()->associate($roofType);

        $building->buildingFeatures()->save($features);

        $elements = $request->get('element', []);

        // the user has not an option / dropdown to set an interest foor the living room windows
        // we will always set the interest level to 1 so the user can still go to the step.
        $livingRoomWindowsElement = Element::where('short', 'living-rooms-windows')->first();
        $yesOnShortTermInterest = Interest::where('calculate_value', 1)->first();
        $buildingOwner = Building::find($buildingId)->user;

        UserInterest::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id'            => $buildingOwner->id,
                'input_source_id'    => $inputSourceId,
                'interested_in_type' => 'element',
                'interested_in_id'   => $livingRoomWindowsElement->id,
            ],
            [
                'interest_id'        => $yesOnShortTermInterest->id,
            ]
        );

        foreach ($elements as $elementId => $elementValueId) {
            $element = Element::find($elementId);
            $elementValue = ElementValue::find($elementValueId);

            // Get the interest field off the element
            $elementInterestId = $request->input('user_interest.element.'.$elementId.'', '');

            if ($element instanceof Element && $elementValue instanceof ElementValue) {
                BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                    [
                        'element_id' => $element->id,
                        'input_source_id' => $inputSourceId,
                        'building_id' => $buildingId,
                    ],
                    [
                        'element_value_id' => $elementValue->id,
                    ]
                );
                if (! empty($elementInterestId)) {
                    // We'll create the user interests for the elements or update it
                    UserInterest::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                        [
                            'user_id'            => $buildingOwner->id,
                            'input_source_id'    => $inputSourceId,
                            'interested_in_type' => 'element',
                            'interested_in_id'   => $elementId,
                        ],
                        [
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
                $buildingService = BuildingService::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                    [
                        'service_id' => $service->id,
                        'input_source_id' => $inputSourceId,
                        'building_id' => $buildingId,
                    ],
                    [
                        'service_id' => $service->id,
                        'input_source_id' => $inputSourceId,
                        'building_id' => $buildingId,
                    ]
                );

                // check if the current service is a sun panel
                // if so, we will need to put the value / valueId inside the extra field.
                if ('total-sun-panels' == $service->short) {
                    //$buildingService->extra = ['value' => $serviceValueId, 'date' => $serviceExtra];
                    $serviceExtra['value'] = (int) $serviceValueId;
                    $buildingService->extra = array_only($serviceExtra, ['value', 'year']);
                }
                // if its a ventilation, is has a dropdown so it has a serviceValue
                elseif ('house-ventilation' == $service->short) {
                    $buildingService->extra = array_only($serviceExtra, ['year']);
                    $buildingService->serviceValue()->associate($serviceValue);
                } else {
                    $buildingService->serviceValue()->associate($serviceValue);
                }

                $buildingService->save();

                // We'll create the user interests for the services or update it
                if (! empty($serviceInterestId)) {
                    UserInterest::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                        [
                            'user_id'            => $buildingOwner->id,
                            'input_source_id'    => $inputSourceId,
                            'interested_in_type' => 'service',
                            'interested_in_id'   => $serviceId,
                        ],
                        [
                            'interest_id'        => $serviceInterestId,
                        ]
                    );
                }
            }
        }

        $motivationCreateData = [];
        // get the motivations
        foreach ($request->get('motivation', []) as $key => $motivationId) {
            $motivationCreateData[] = [
                'motivation_id' => $motivationId,
                'order' => $key,
            ];
        }
        ModelService::deleteAndCreate(UserMotivation::class,
            [
                'user_id' => $buildingOwner->id,
            ],
            $motivationCreateData
        );

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $buildingOwner->id,
                'input_source_id' => $inputSourceId,
            ],
            [
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
            ]
        );

        // Save progress
        StepHelper::complete($this->step, $building, HoomdossierSession::getInputSource(true));
        StepDataHasBeenChanged::dispatch($this->step, $building, Hoomdossier::user());

        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep($building, HoomdossierSession::getInputSource(true), $this->step);
        $url = $nextStep['url'];

        if (! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }
}
