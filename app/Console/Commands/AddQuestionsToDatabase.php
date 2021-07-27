<?php

namespace App\Console\Commands;

use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Models\BuildingHeating;
use App\Models\BuildingHeatingApplication;
use App\Models\BuildingType;
use App\Models\Element;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InsulatingGlazing;
use App\Models\PaintworkStatus;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\SubStepToolQuestion;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\ToolQuestionType;
use App\Models\ToolQuestionValueable;
use App\Models\WoodRotStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AddQuestionsToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:to-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        \Schema::disableForeignKeyConstraints();
        ToolQuestionValueable::truncate();
        ToolQuestion::truncate();
        ToolQuestionCustomValue::truncate();
        \Schema::enableForeignKeyConstraints();
        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::findByShort('living-rooms-windows');
        $sleepingRoomsWindows = Element::findByShort('sleeping-rooms-windows');
        // General data - Services (that are not queried later on step basis)
        $heatPump = Service::findByShort('heat-pump');
        $ventilation = Service::findByShort('house-ventilation');
        $buildingHeatingApplications = BuildingHeatingApplication::orderBy('order')->get();

        // Wall insulation
        $wallInsulation = Element::findByShort('wall-insulation');
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $energyLabels = EnergyLabel::all();

        // Insulated glazing
        $insulatedGlazings = InsulatingGlazing::all();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $crackSealing = Element::findByShort('crack-sealing');
        $frames = Element::findByShort('frames');
        $woodElements = Element::findByShort('wood-elements');
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        // High efficiency boiler
        // NOTE: building element hr-boiler tells us if it's there
        $hrBoiler = Service::findByShort('hr-boiler');
        $boiler = Service::findByShort('boiler');

        // Solar panels
        $solarPanels = Service::findByShort('total-sun-panels');
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        $heater = Service::findByShort('sun-boiler');
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();


        // Floor insulation
        /** @var Element $floorInsulation */
        $floorInsulation = Element::findByShort('floor-insulation');
        $crawlspace = Element::findByShort('crawlspace');

        // Roof insulation
        $roofInsulation = Element::findByShort('roof-insulation');
        $roofTypes = RoofType::all();
        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        $buildingTypes = BuildingType::all();
        $radioIconType = ToolQuestionType::findByShort('radio-icon');
        $radioType = ToolQuestionType::findByShort('radio');
        $textType = ToolQuestionType::findByShort('text');
        $sliderType = ToolQuestionType::findByShort('slider');
        $textareaType = ToolQuestionType::findByShort('textarea');
        $measurePriorityType = ToolQuestionType::findByShort('measure-priority');

        $templateDefault = SubStepTemplate::findByShort('template-default');
        $template2rows1top2bottom = SubStepTemplate::findByShort('template-2-rows-1-top-2-bottom');
        $template2rows2top1bottom = SubStepTemplate::findByShort('template-2-rows-1-top-2-bottom');
        $templateCustomChanges = SubStepTemplate::findByShort('template-custom-changes');

        $structure = [
            'building-data' => [
                // sub step name
                // sub step name
                'Wat voor woning' => [
                    // question data
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:building_types,id'],
                            'save_in' => 'building_features.building_type_id',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.building-type',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $buildingTypes,
                        ],
                    ]
                ],
                // wat voor type aappartament heeft u moet nog komen.
                'Wat voor dak' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:roof_types,id'],
                            'save_in' => 'building_features.roof_type_id',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.roof-type',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => RoofType::all(),
                        ]
                    ]
                ],
                'Bouwjaar en oppervlak' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['numeric', 'between:1900,' . date('Y')],
                            'save_in' => 'building_features.build_year',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.build-year',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 1900, 'max' => date('Y'), 'value' => 1930, 'step' => 10],
                        ],
                        [
                            'validation' => ['numeric', 'between:1,5'],
                            'save_in' => 'building_features.building_layers',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.building-layers',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 1, 'max' => 10, 'value' => 4, 'step' => 1],
                        ],
                    ]
                ],
                'Monument en energielabel' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['numeric', 'between:1900,' . date('Y')],
                            'save_in' => 'building_features.monument',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.monument',
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_custom_values' => [
                                1 => __('woningdossier.cooperation.radiobutton.yes'),
                                2 => __('woningdossier.cooperation.radiobutton.no'),
                                0 => __('woningdossier.cooperation.radiobutton.unknown'),
                            ],
                        ],
                        [
                            'validation' => ['numeric', 'exists:energy_labels,id'],
                            'save_in' => 'building_features.energy_label_id',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.energy-label',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => EnergyLabel::all(),
                        ],
                    ]
                ],
                'Gebruikersoppervlak en bijzonderheden' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['numeric', 'min:20', 'max:999999'],
                            'save_in' => 'building_features.surface',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface',
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            // todo: find the right column to save this at, this is "zijn er nog bijzonderheden oevr de woning"
                            'validation' => ['numeric', 'min:20', 'max:999999'],
                            'save_in' => 'building_features.surface',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface',
                            'tool_question_type_id' => $textareaType->id,
                        ],
                    ]
                ],
            ],
            'usage-quick-scan' => [
                'Hoeveel bewoners' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required'],
                            'save_in' => 'user_energy_habits.resident_count',
                            'translation' => 'Hoeveel mensen wonen er in de woning',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_custom_values' => [
                                1 => 'Alleen',
                                2 => 'Twee',
                                3 => 'Vier',
                                5 => 'Vijf',
                                6 => 'Zes',
                                7 => 'Zeven',
                                0 => 'Meer dan zeven',
                            ],
                        ],
                    ]
                ],
                'Thermostaat gebruik' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'save_in' => 'user_energy_habits.thermostat_high',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-high',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 10, 'max' => 30, 'value' => 22, 'step' => 1],
                            'unit_of_measure' => __('general.unit.degrees.title'),
                        ],
                        [
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'save_in' => 'user_energy_habits.thermostat_low',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-low',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 10, 'max' => 30, 'value' => 12, 'step' => 1],
                            'unit_of_measure' => __('general.unit.degrees.title'),
                        ],
                        [

                            'validation' => ['required', 'numeric', 'between:1,24'],
                            'save_in' => 'user_energy_habits.hours_high',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.hours-high',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 0, 'max' => 24, 'value' => 12, 'step' => 1],
                        ],
                    ]
                ],
                'Gebruik warm tapwater' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:building_heatings,id'],
                            'save_in' => 'user_energy_habits.heating_first_floor',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.heating-first-floor',
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => BuildingHeating::all(),
                        ],
                        [
                            'validation' => ['required', 'exists:building_heatings,id'],
                            'save_in' => 'user_energy_habits.heating_second_floor',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.heating-second-floor',
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => BuildingHeating::all(),
                        ],
                    ]
                ],
                'Gas en elektra gebruik' => [
                    'sub_step_template_id' => $template2rows1top2bottom->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:tool_question_custom_values,id'],
                            'short' => 'cook-type',
                            'translation' => "Hoe wordt er gekookt?",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_custom_values' => [
                                'gas' => 'Gas',
                                'electric' => 'Elektrisch',
                                'induction' => 'Inductie',
                            ],
                        ],
                        [
                            'validation' => ['required', 'numeric', 'min:0', 'max:10000'],
                            'save_in' => 'user_energy_habits.amount_gas',
                            'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.gas-usage',
                            'tool_question_type_id' => $textType->id,
                            'unit_of_measure' => __('general.unit.cubic-meters.title'),
                        ],
                        [
                            'validation' => ['required', 'numeric', 'min:0', 'max:10000'],
                            'save_in' => 'user_energy_habits.amount_electricity',
                            'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.amount-electricity',
                            'tool_question_type_id' => $textType->id,
                            'unit_of_measure' => 'kWh'
                        ],
                    ]
                ],
            ],
            'living-requirements' => [
                'Hoelang blijven wonen' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            // note: new question
                            'short' => 'remaining-living-years',
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'translation' => 'Hoeveel jaar denkt u hier nog te blijven wonen',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 1, 'max' => 10, 'value' => 7, 'step' => 1],
                        ],

                    ]
                ],
                'Welke zaken vind u belangenrijk?' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'comfort-priority',
                            'translation' => "Comfort",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'investment-priority',
                            'translation' => "Goede investering",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'adjust-to-liking-priority',
                            'translation' => "Naar eigen smaak maken",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'sustainability-priority',
                            'translation' => "Duurzaamheid",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'to-lower-expenses-priority',
                            'translation' => "Verlaging maandlasten",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                        [
                            'validation' => ['required', 'in:1,2,3,4,5'],
                            'short' => 'indoor-climate-priority',
                            'translation' => "Gezond binnenklimaat",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => ['min' => 1, 'max' => 5, 'value' => 1],
                        ],
                    ],

                ],
                'Welke zaken vervangen' => [
                    // note: dit is een custom vraag, zie slide 18
                    'sub_step_template_id' => $templateCustomChanges->id,
//                            'options' => ['min' => 1, 'max' => 10, 'value' => 5, 'step' => 1],
                ],
            ],
            'residential-status' => [
                'Muurisolatie' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$wallInsulation->id}.element_value_id",
                            'translation' => "Wat is de staat van de muurisolatie",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $wallInsulation->values()->orderBy('order')->get()
                        ],
                    ]
                ],
                'Vloerisolatie' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$floorInsulation->id}.element_value_id",
                            'translation' => "Wat is de staat van de vloerisolatie",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $floorInsulation->values()->orderBy('order')->get()
                        ],
                    ]
                ],
                'Dakisolatie' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$roofInsulation->id}.element_value_id",
                            'translation' => "Wat is de staat van de dakisolatie",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $roofInsulation->values()->orderBy('order')->get()
                        ],
                    ]
                ],
                'Glasisolatie eerste woonlaag' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$livingRoomsWindows->id}.element_value_id",
                            'translation' => "Welke glasisolatie heeft u op de eerste woonlaag",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $livingRoomsWindows->values()->orderBy('order')->get()
                        ],
                    ]
                ],
                'Glasisolatie tweede woonlaag' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$sleepingRoomsWindows->id}.element_value_id",
                            'translation' => "Welke glasisolatie heeft u op de tweede woonlaag",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $sleepingRoomsWindows->values()->orderBy('order')->get()
                        ],
                    ]
                ],
                'Verwarming' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:tool_question_custom_values,id'],
                            'short' => 'heat-source',
                            'translation' => "Wat gebruikt u voor de verwarming en warm water?",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_custom_values' => [
                                'hr-boiler' => 'Gasketel',
                                'heat-pump' => 'Warmtepomp',
                                'infrared' => 'Infrarood',
                                'district-heating' => 'Stadsverwarming',
                            ],
                        ],
                    ]
                ],
                'Gasketel vragen' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            'column' => 'heat-source',
                            'operator' => '=',
                            'value' => 'hr-boiler',
                        ]
                    ],
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'save_in' => "building_services.{$boiler->id}.service_value_id",
                            'short' => 'boiler-type',
                            'translation' => "Wat voor gasketel heeft u?",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $boiler->values()->orderBy('order')->get(),
                        ],
                        [
                            'validation' => ['nullable', 'numeric', 'between:1970,'.date('Y'),],
                            'save_in' => "building_services.{$boiler->id}.extra.date",
                            'short' => 'boiler-placed-date',
                            'translation' => "Wanneer is de gasketel geplaatst",
                            'tool_question_type_id' => $textType->id,
                        ],
                    ]
                ],
                'Hoe is de verwarming' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'save_in' => "building_features.building_heating_application_id",
                            'short' => 'heat-source',
                            // was current-state -> hoe word de woning nu verwarmd
                            'translation' => "Hoe is de verwarming",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $buildingHeatingApplications,
                        ],
                    ]
                ],
                'Zonnenboiler' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'save_in' => "building_services.{$heater->id}.service_value_id",
                            'short' => 'heater-type',
                            'translation' => "Heeft u een zonneboiler",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $heater->values()->orderBy('order')->get(),
                        ],
                    ]
                ],
                'Warmtepomp' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            'column' => 'heat-source',
                            'operator' => '=',
                            'value' => 'heat-pump',
                        ]
                    ],
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'save_in' => "building_services.{$heatPump->id}.service_value_id",
                            'short' => 'heat-pump-type',
                            'translation' => "Heeft u een warmptepomp",
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => $heater->values()->orderBy('order')->get(),
                        ],
                        [
                            'validation' => [
                                // required when the heat pump is available
                                "required_if:building_services.{$heatPump->id}.service_value_id,!=,".$heater->values()->where('calculate_value', 1)->first()->id,
                                'numeric',
                                'between:1900,' . date('Y')
                            ],
                            'short' => 'heat-pump-placed-date',
                            'placeholder' => 'Voer een jaartal in',
                            'translation' => "Wanneer is de warmtepomp geplaatst?",
                            'tool_question_type_id' => $textType->id,
                        ],
                    ]
                ],
                'Ventilatie' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'save_in' => "building_services.{$ventilation->id}.service_value_id",
                            'short' => 'ventilation-type',
                            // was current-state -> hoe word het huis geventileerd
                            'translation' => "Heeft u ventilatie?",
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => $ventilation->values()->orderBy('order')->get(),
                        ],
                        [
                            'save_in' => "building_elements.{$crackSealing->id}.element_value_id",
                            'validation' => ['required', "exists:elements,id",],
                            'short' => 'crack-sealing-type',
                            // was current-state -> zijn de ramen en deuren voorzien van kierdichting
                            'translation' => "Heeft u kierdichting?",
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => $crackSealing->values()->orderBy('order')->get(),
                        ],
                    ]
                ],
                'Zonnepanelen' => [
                    'sub_step_template_id' => $template2rows2top1bottom->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:services,id'],
                            'short' => 'has-solar-panels',
                            // was current-state -> hoe word het huis geventileerd
                            'translation' => "Heeft u zonnepanelen",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_custom_values' => [
                                'yes' => 'Ja',
                                'no' => 'Nee'
                            ],
                        ],
                        [
                            'validation' => ["required_if:has_solar_panels,yes", 'numeric', 'min:1', 'max:50'],
                            'save_in' => "building_services.{$solarPanels->id}.service_value_id",
                            // was current-state -> hoeveel zonnepanelen zijn er aanwezig
                            'translation' => "Hoeveel zonnepanelen?",
                            'tool_question_type_id' => $textType->id,
                            'tool_question_custom_values' => [
                                'yes' => 'Ja',
                                'no' => 'Nee'
                            ],
                            'conditions' => [
                                [
                                    'column' => 'has-solar-panels',
                                    'operator' => '=',
                                    'value' => 'yes',
                                ]
                            ],
                        ],
                        [
                            'validation' => ["required_if:has_solar_panels,yes", 'numeric', 'min:1', 'max:50'],
                            'save_in' => "building_pv_panels.total_installed_power",
                            // was current-state -> Geinstalleerd vermogen (totaal)
                            'translation' => "Totaal vermogen",
                            'unit_of_measure' => 'WP',
                            'tool_question_type_id' => $textType->id,
                            'conditions' => [
                                [
                                    'column' => 'has-solar-panels',
                                    'operator' => '=',
                                    'value' => 'yes',
                                ]
                            ],
                        ],
                        [
                            'validation' => [
                                "required_if:has_solar_panels,yes",
                                'numeric',
                                'between:1900,' . date('Y')
                            ],
                            'save_in' => "building_services.{$solarPanels->id}.extra.year",
                            'short' => 'solar-panels-placed-date',
                            // was current-state -> Geinstalleerd vermogen (totaal)
                            'translation' => "Wanneer zijn de zonnepanelen geplaatst",
                            'placeholder' => 'Voer een jaartal in',
                            'unit_of_measure' => 'WP',
                            'tool_question_type_id' => $textType->id,
                            'conditions' => [
                                [
                                    'column' => 'has-solar-panels',
                                    'operator' => '=',
                                    'value' => 'yes',
                                ]
                            ],
                        ],
                    ]
                ],
            ],
        ];
        foreach ($structure as $stepShort => $subQuestions) {
            $this->info("Adding questions to {$stepShort}..     ");
            $step = Step::findByShort($stepShort);
            $orderForSubQuestions = 0;
            foreach ($subQuestions as $subQuestionName => $subQuestionData) {

                $subStepData = [
                    'name' => ['nl' => $subQuestionName],
                    'order' => $orderForSubQuestions,
                    'step_id' => $step->id,
                    'sub_step_template_id' => $subQuestionData['sub_step_template_id'],
                ];

                if (isset($subQuestionData['conditions'])) {
                    $subStepData['conditions'] = $subQuestionData['conditions'];
                }

                $subStep = SubStep::create($subStepData);

                if (isset($subQuestionData['questions'])) {
                    foreach ($subQuestionData['questions'] as $questionData) {
                        // create the question itself
                        $questionData['name'] = [
                            'nl' => __($questionData['translation'] . '.title'),
                        ];
                        $questionData['help_text'] = [
                            'nl' => __($questionData['translation'] . '.help'),
                        ];

                        // when the short is not set, we will use the column name as this describes it clearly
                        if (!isset($questionData['short'])) {
                            $questionData['short'] = last(explode('.', $questionData['save_in']));
                        }
                        /** @var ToolQuestion $toolQuestion */
                        $toolQuestion = ToolQuestion::create(
                            Arr::except($questionData, ['tool_question_values', 'tool_question_custom_values'])
                        );

                        $subStep->toolQuestions()->attach($toolQuestion, ['order' => $orderForSubQuestions]);

                        if (isset($questionData['tool_question_custom_values'])) {
                            $toolQuestionCustomValueOrder = 0;
                            foreach ($questionData['tool_question_custom_values'] as $value => $name) {

                                $toolQuestion->toolQuestionCustomValues()->create([
                                    'order' => $toolQuestionCustomValueOrder,
                                    'show' => true,
                                    // so we will compare the short to determine what is what, but we will keep value for now
                                    'short' => $value,
                                    'value' => $value,
                                    'name' => $name
                                ]);
                            }
                        }

                        if (isset($questionData['tool_question_values'])) {
                            foreach ($questionData['tool_question_values'] as $toolQuestionValueOrder => $toolQuestionValue) {
                                $toolQuestion->toolQuestionValueables()->create([
                                    'order' => $toolQuestionValueOrder,
                                    'show' => true,
                                    'tool_question_valueable_type' => get_class($toolQuestionValue),
                                    'tool_question_valueable_id' => $toolQuestionValue->id,
                                ]);
                            }
                        }
                    }
                }

                $orderForSubQuestions++;
            }
        }
    }
}
