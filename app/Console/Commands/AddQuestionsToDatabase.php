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
use App\Models\ToolQuestionValuable;
use App\Models\WoodRotStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
    protected $description = 'Adds all questions to the database';

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
        ToolQuestionValuable::truncate();
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
        $heatings = BuildingHeating::all();
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
        $radioIconSmallType = ToolQuestionType::findByShort('radio-icon-small');
        $radioType = ToolQuestionType::findByShort('radio');
        $textType = ToolQuestionType::findByShort('text');
        $sliderType = ToolQuestionType::findByShort('slider');
        $textareaType = ToolQuestionType::findByShort('textarea');
        $measurePriorityType = ToolQuestionType::findByShort('rating-slider');

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
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    2 => [
                                        'icon' => 'icon-detached-house',
                                    ],
                                    3 => [
                                        'icon' => 'icon-two-under-one-roof',
                                    ],
                                    4 => [
                                        'icon' => 'icon-end-of-terrace-house',
                                    ],
                                    5 => [
                                        'icon' => 'icon-mid-terrace-house',
                                    ],
                                    6 => [
                                        'icon' => 'icon-apartment-ground-floor-corner', // TODO: See below
                                    ],
                                    7 => [
                                        'icon' => 'icon-apartment-ground-floor-between', // TODO: See below
                                    ],
                                    8 => [
                                        'icon' => 'icon-upstairs-apartment-between',
                                    ],
                                    9 => [
                                        'icon' => 'icon-upstairs-apartment-corner',
                                    ],
                                    10 => [
                                        'icon' => 'icon-apartment-mid-floor-between', // TODO: See below
                                    ],
                                    11 => [
                                        'icon' => 'icon-apartment-mid-floor-corner', // TODO: See below
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: wat voor type appartament heeft u moet nog komen. Dit moet de vorige vraag aanpassen, gezien de vorige vraag de optie van het type appartement nu al aangeeft
                'Wat voor dak' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:roof_types,id'],
                            'save_in' => 'building_features.roof_type_id',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.roof-type',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $roofTypes,
                            'extra' => [
                                'column' => 'short',
                                'data' => [
                                    'gabled-roof'  => [
                                        'icon' => 'icon-pointed-roof'
                                    ],
                                    'pitched' => [
                                        'icon' => 'icon-pitched-roof',
                                    ],
                                    'flat' => [
                                        'icon' => 'icon-flat-roof',
                                    ],
                                    'flat-pitched-roof'  => [
                                        'icon' => 'icon-flat-pitched-roof'
                                    ],
                                    'rounded-roof' => [
                                        'icon' => 'icon-rounded-roof'
                                    ],
                                    'straw-roof' => [
                                        // todo: add rieten dak
                                        'icon' => 'icon-pointed-roof'
                                    ],
                                    'none' => [
                                        'icon' => 'icon-other',
                                    ],
                                ],
                            ],
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
                                1 => [
                                    'name' => __('woningdossier.cooperation.radiobutton.yes'),
                                    'extra' => [],
                                ],
                                2 => [
                                    'name' => __('woningdossier.cooperation.radiobutton.no'),
                                    'extra' => [],
                                ],
                                0 => [
                                    'name' => __('woningdossier.cooperation.radiobutton.unknown'),
                                    'extra' => [],
                                ],
                            ],
                        ],
                        [
                            'validation' => ['numeric', 'exists:energy_labels,id'],
                            'save_in' => 'building_features.energy_label_id',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.energy-label',
                            'tool_question_type_id' => $radioIconSmallType->id,
                            'tool_question_values' => $energyLabels,
                            'extra' => [
                                'column' => 'name',
                                'data' => [
                                    'A' => [
                                        'icon' => 'icon-label-a',
                                    ],
                                    'B' => [
                                        'icon' => 'icon-label-b',
                                    ],
                                    'C' => [
                                        'icon' => 'icon-label-c',
                                    ],
                                    'D' => [
                                        'icon' => 'icon-label-d',
                                    ],
                                    'E' => [
                                        'icon' => 'icon-label-e',
                                    ],
                                    'F' => [
                                        'icon' => 'icon-label-f',
                                    ],
                                    'G' => [
                                        'icon' => 'icon-label-g',
                                    ],
                                ],
                            ],
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
//                        [
//                             todo: find the right column to save this at, this is "zijn er nog bijzonderheden oevr de woning"
//                            'validation' => ['numeric', 'min:20', 'max:999999'],
//                            'save_in' => 'building_features.surface',
//                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface',
//                            'tool_question_type_id' => $textareaType->id,
//                        ],
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
                                1 => [
                                    'name' => 'Alleen',
                                    'extra' => [
                                        'icon' => 'icon-persons-one',
                                    ],
                                ],
                                2 => [
                                    'name' => 'Twee',
                                    'extra' => [
                                        'icon' => 'icon-persons-two',
                                    ],
                                ],
                                3 => [
                                    'name' => 'Drie',
                                    'extra' => [
                                        'icon' => 'icon-persons-three',
                                    ],
                                ],
                                4 => [
                                    'name' => 'Vier',
                                    'extra' => [
                                        'icon' => 'icon-persons-four',
                                    ],
                                ],
                                5 => [
                                    'name' => 'Vijf',
                                    'extra' => [
                                        'icon' => 'icon-persons-five',
                                    ],
                                ],
                                6 => [
                                    'name' => 'Zes',
                                    'extra' => [
                                        'icon' => 'icon-persons-six',
                                    ],
                                ],
                                7 => [
                                    'name' => 'Zeven',
                                    'extra' => [
                                        'icon' => 'icon-persons-seven',
                                    ],
                                ],
                                0 => [
                                    'name' => 'Meer dan zeven',
                                    'extra' => [
                                        'icon' => 'icon-persons-more-than-seven',
                                    ],
                                ],
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
                            'unit_of_measure' => '°',
                        ],
                        [
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'save_in' => 'user_energy_habits.thermostat_low',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-low',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 10, 'max' => 30, 'value' => 12, 'step' => 1],
                            'unit_of_measure' => '°',
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
                            'tool_question_values' => $heatings,
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                    5 => [],
                                ],
                            ],
                        ],
                        [
                            'validation' => ['required', 'exists:building_heatings,id'],
                            'save_in' => 'user_energy_habits.heating_second_floor',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.heating-second-floor',
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => $heatings,
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                    5 => [],
                                ],
                            ],
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
                                'gas' => [
                                    'name' => 'Gas',
                                    'extra' => [
                                        'icon' => 'icon-gas',
                                    ],
                                ],
                                'electric' => [
                                    'name' => 'Elektrisch',
                                    'extra' => [
                                        'icon' => 'icon-electric',
                                    ],
                                ],
                                'induction' => [
                                    'name' => 'Inductie',
                                    'extra' => [
                                        'icon' => 'icon-induction',
                                    ],
                                ],
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
                            'translation' => "Welke zaken vindt u belangrijk?",
                            'tool_question_type_id' => $measurePriorityType->id,
                            'options' => [
                                [
                                    'name' => 'Comfort',
                                    'short' => 'comfort',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                                [
                                    'name' => 'Duurzaamheid',
                                    'short' => 'renewable',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                                [
                                    'name' => 'Goede investering',
                                    'short' => 'investment',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                                [
                                    'name' => 'Verlaging maandlasten',
                                    'short' => 'lower-monthly-costs',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                                [
                                    'name' => 'Naar eigen smaak maken',
                                    'short' => 'to-own-taste',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                                [
                                    'name' => 'Gezond binnenklimaat',
                                    'short' => 'indoor-climate',
                                    'min' => 1,
                                    'max' => 5,
                                    'value' => 1,
                                ],
                            ],
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
                            'tool_question_values' => $wallInsulation->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [
                                        'icon' => 'icon-other',
                                    ],
                                    2 => [
                                        'icon' => 'icon-wall-insulation-none',
                                    ],
                                    3 => [
                                        'icon' => 'icon-wall-insulation-moderate',
                                    ],
                                    4 => [
                                        'icon' => 'icon-wall-insulation-good',
                                    ],
                                    5 => [
                                        'icon' => 'icon-wall-insulation-excellent',
                                    ],
                                ],
                            ],
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
                            'tool_question_values' => $floorInsulation->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [
                                        'icon' => 'icon-other',
                                    ],
                                    2 => [
                                        'icon' => 'icon-floor-insulation-none',
                                    ],
                                    3 => [
                                        'icon' => 'icon-floor-insulation-moderate',
                                    ],
                                    4 => [
                                        'icon' => 'icon-floor-insulation-good',
                                    ],
                                    5 => [
                                        'icon' => 'icon-floor-insulation-excellent',
                                    ],
                                    6 => [
                                        'icon' => 'icon-other',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: Niet van toepassing is niet zichtbaar in het design
                'Dakisolatie' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$roofInsulation->id}.element_value_id",
                            'translation' => "Wat is de staat van de dakisolatie",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $roofInsulation->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [
                                        'icon' => 'icon-other',
                                    ],
                                    2 => [
                                        'icon' => 'icon-roof-insulation-none',
                                    ],
                                    3 => [
                                        'icon' => 'icon-roof-insulation-moderate',
                                    ],
                                    4 => [
                                        'icon' => 'icon-roof-insulation-good',
                                    ],
                                    5 => [
                                        'icon' => 'icon-roof-insulation-excellent',
                                    ],
                                    6 => [
                                        'icon' => 'icon-other',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: Niet van toepassing is niet zichtbaar in het design
                'Glasisolatie eerste woonlaag' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$livingRoomsWindows->id}.element_value_id",
                            'translation' => "Welke glasisolatie heeft u op de eerste woonlaag",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $livingRoomsWindows->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'order',
                                'data' => [
                                    0 => [
                                        'icon' => 'icon-glass-single',
                                    ],
                                    1 => [
                                        'icon' => 'icon-glass-double',
                                    ],
                                    2 => [
                                        'icon' => 'icon-glass-hr-dp',
                                    ],
                                    3 => [
                                        'icon' => 'icon-glass-hr-tp', // TODO: Drievoudig glas, is dat hetzelfde als hr+++?
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: Meer glas opties in design dan in de database
                'Glasisolatie tweede woonlaag' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:elements,id'],
                            'save_in' => "building_elements.{$sleepingRoomsWindows->id}.element_value_id",
                            'translation' => "Welke glasisolatie heeft u op de tweede woonlaag",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $sleepingRoomsWindows->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'order',
                                'data' => [
                                    0 => [
                                        'icon' => 'icon-glass-single',
                                    ],
                                    1 => [
                                        'icon' => 'icon-glass-double',
                                    ],
                                    2 => [
                                        'icon' => 'icon-glass-hr-dp',
                                    ],
                                    3 => [
                                        'icon' => 'icon-glass-hr-tp', // TODO: Drievoudig glas, is dat hetzelfde als hr+++?
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: Meer glas opties in design dan in de database
                'Verwarming' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:tool_question_custom_values,id'],
                            'short' => 'heat-source',
                            'translation' => "Wat gebruikt u voor de verwarming en warm water?",
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_custom_values' => [
                                'hr-boiler' => [
                                    'name' => 'Gasketel',
                                    'extra' => [
                                        'icon' => 'icon-central-heater-gas',
                                    ],
                                ],
                                'heat-pump' => [
                                    'name' => 'Warmtepomp',
                                    'extra' => [
                                        'icon' => 'icon-heat-pump',
                                    ],
                                ],
                                'infrared' => [
                                    'name' => 'Infrarood',
                                    'extra' => [
                                        'icon' => 'icon-infrared-heater',
                                    ],
                                ],
                                'district-heating' => [
                                    'name' => 'Stadsverwarming',
                                    'extra' => [
                                        'icon' => 'icon-district-heating',
                                    ],
                                ],
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
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [],
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                    5 => [],
                                ],
                            ],
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
                            'extra' => [
                                'column' => 'short',
                                'data' => [
                                    'radiators' => [
                                        'icon' => 'icon-radiator',
                                    ],
                                    'radiators-with-floor-heating' => [
                                        'icon' => 'icon-radiant-floor-heating',
                                    ],
                                    'low-temperature-heater' => [
                                        'icon' => 'icon-radiator-low-temp',
                                    ],
                                    'floor-wall-heating' => [
                                        'icon' => 'icon-radiant-wall-heating',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                // TODO: Meer/andere opties in design dan in datbase
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
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [
                                        'icon' => 'icon-sun-boiler-none',
                                    ],
                                    2 => [
                                        'icon' => 'icon-sun-boiler-hot-water',
                                    ],
                                    3 => [
                                        'icon' => 'icon-sun-boiler-heating',
                                    ],
                                    4 => [
                                        'icon' => 'icon-sun-boiler-both',
                                    ],
                                ],
                            ],
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
                            'tool_question_values' => $heatPump->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [],
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                    5 => [],
                                ],
                            ],
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
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [],
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                ],
                            ],
                        ],
                        // TODO: Andere opties in design dan in database
                        [
                            'save_in' => "building_elements.{$crackSealing->id}.element_value_id",
                            'validation' => ['required', "exists:elements,id",],
                            'short' => 'crack-sealing-type',
                            // was current-state -> zijn de ramen en deuren voorzien van kierdichting
                            'translation' => "Heeft u kierdichting?",
                            'tool_question_type_id' => $radioType->id,
                            'tool_question_values' => $crackSealing->values()->orderBy('order')->get(),
                            'extra' => [
                                'column' => 'calculate_value',
                                'data' => [
                                    1 => [],
                                    2 => [],
                                    3 => [],
                                    4 => [],
                                ],
                            ],
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
                                'yes' => [
                                    'name' => 'Ja',
                                    'extra' => [
                                        'icon' => 'icon-solar-panels',
                                    ],
                                ],
                                'no' => [
                                    'name' => 'Nee',
                                    'extra' => [
                                        'icon' => 'icon-solar-panels-none',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'validation' => ["required_if:has_solar_panels,yes", 'numeric', 'min:1', 'max:50'],
                            'save_in' => "building_services.{$solarPanels->id}.service_value_id",
                            // was current-state -> hoeveel zonnepanelen zijn er aanwezig
                            'translation' => "Hoeveel zonnepanelen?",
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
                    'order' =>  $orderForSubQuestions,
                    'slug' => ['nl' => Str::slug($subQuestionName)],
                    'step_id' => $step->id,
                    'sub_step_template_id' => $subQuestionData['sub_step_template_id'],
                ];

                if (isset($subQuestionData['conditions'])) {
                    $subStepData['conditions'] = $subQuestionData['conditions'];
                }

                $subStep = SubStep::create($subStepData);

                if (isset($subQuestionData['questions'])) {
                    $orderForSubStepToolQuestions = 0;
                    foreach ($subQuestionData['questions'] as $questionData) {
                        // create the question itself

                        // Translation can be a key or text. We compare the results, because if it's a key, then the
                        // result will be different
                        $translation = $questionData['translation'];
                        $name = __($translation . '.title');
                        $name = $name === $translation . '.title' ? $translation : $name;
                        $help = __($questionData['translation'] . '.help');
                        $help = $help === $translation . '.help' ? $translation : $help;

                        $questionData['name'] = [
                            'nl' => $name,
                        ];
                        $questionData['help_text'] = [
                            'nl' => $help,
                        ];
                        // when the short is not set, we will use the column name as this describes it clearly
                        if (!isset($questionData['short'])) {
                            $questionData['short'] = last(explode('.', $questionData['save_in']));
                        }
                        /** @var ToolQuestion $toolQuestion */
                        $toolQuestion = ToolQuestion::create(
                            Arr::except($questionData, ['tool_question_values', 'tool_question_custom_values', 'extra'])
                        );

                        $subStep->toolQuestions()->attach($toolQuestion, ['order' => $orderForSubStepToolQuestions]);

                        if (isset($questionData['tool_question_custom_values'])) {
                            $toolQuestionCustomValueOrder = 0;
                            foreach ($questionData['tool_question_custom_values'] as $value => $customValueData) {
                                $name = $customValueData['name'];
                                $extra = $customValueData['extra'] ?? [];
                                $toolQuestion->toolQuestionCustomValues()->create([
                                    'order' => $toolQuestionCustomValueOrder,
                                    'show' => true,
                                    // so we will compare the short to determine what is what, but we will keep value for now
                                    'short' => $value,
                                    'value' => $value,
                                    'name' => [
                                        'nl' => $name,
                                    ],
                                    'extra' => $extra,
                                ]);
                                $toolQuestionCustomValueOrder++;
                            }
                        }

                        if (isset($questionData['tool_question_values'])) {
                            $extra = $questionData['extra'];

                            foreach ($questionData['tool_question_values'] as $toolQuestionValueOrder => $toolQuestionValue) {
                                $toolQuestion->toolQuestionValuables()->create([
                                    'order' => $toolQuestionValueOrder,
                                    'show' => true,
                                    'tool_question_valuable_type' => get_class($toolQuestionValue),
                                    'tool_question_valuable_id' => $toolQuestionValue->id,
                                    // We grab the extra data by the set column (e.g. calculate_value)
                                    'extra' => $extra['data'][$toolQuestionValue->{$extra['column']}]
                                ]);
                            }
                        }
                        $orderForSubStepToolQuestions++;
                    }
                }

                $orderForSubQuestions++;
            }
        }
    }
}
