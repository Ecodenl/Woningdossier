<?php

use App\Helpers\Conditions\Clause;
use App\Helpers\DataTypes\Caster;
use App\Models\BuildingHeating;
use App\Models\BuildingType;
use App\Models\BuildingTypeCategory;
use App\Models\ComfortLevelTapWater;
use App\Models\Element;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\PvPanelOrientation;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\ToolQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ToolQuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First, we need to fetch all relevant models for the tool questions

        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::findByShort('living-rooms-windows');
        $sleepingRoomsWindows = Element::findByShort('sleeping-rooms-windows');
        // General data - Services (that are not queried later on step basis)
        $heatPump = Service::findByShort('heat-pump');
        $ventilation = Service::findByShort('house-ventilation');

        // Wall insulation
        $wallInsulation = Element::findByShort('wall-insulation');
        $energyLabels = EnergyLabel::ordered()->get();
        $comfortLevelsTapWater = ComfortLevelTapWater::where('calculate_value', '<=', 3)->get();

        // Insulated glazing
        $heatings = BuildingHeating::all();
        $crackSealing = Element::findByShort('crack-sealing');

        // High efficiency boiler
        $boiler = Service::findByShort('boiler');

        // Solar panels
        $solarPanels = Service::findByShort('total-sun-panels');

        // Floor insulation
        $floorInsulation = Element::findByShort('floor-insulation');

        // Roof insulation
        $roofInsulation = Element::findByShort('roof-insulation');
        $roofTypes = RoofType::orderBy('order')->get();
        $buildingTypes = BuildingType::all();

        // Sun boiler
        $collectorOrientations = PvPanelOrientation::orderBy('order')->get();

        // Input sources
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        // Quick scan steps
        $stepBuildingData = Step::findByShort('building-data');
        $stepUsageQuickScan = Step::findByShort('usage-quick-scan');
        $stepLivingRequirements = Step::findByShort('living-requirements');
        $stepResidentialStatus = Step::findByShort('residential-status');

        // Expert scan steps
        $hrBoilerStep = Step::findByShort('high-efficiency-boiler');
        $sunBoilerStep = Step::findByShort('heater');
        $heatPumpStep = Step::findByShort('heat-pump');

        $questions = [
            #-------------------------
            # Quick-scan only / shared with expert-scan questions
            #-------------------------
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:building_type_categories,id'],
                'save_in' => 'building_features.building_type_category_id',
                'short' => 'building-type-category',
                'translation' => 'Wat voor soort woning heeft u?',
                'tool_question_values' => BuildingTypeCategory::all(),
                'extra' => [
                    'column' => 'short',
                    'data' => [
                        'detached-house' => [
                            'icon' => 'icon-detached-house',
                        ],
                        '2-homes-under-1-roof' => [
                            'icon' => 'icon-two-under-one-roof',
                        ],
                        'corner-house' => [
                            'icon' => 'icon-end-of-terrace-house',
                        ],
                        'terraced-house' => [
                            'icon' => 'icon-mid-terrace-house',
                        ],
                        'apartment' => [
                            'icon' => 'icon-apartment-mid-floor-between',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:building_types,id'],
                'save_in' => 'building_features.building_type_id',
                'short' => 'building-type',
                'translation' => "Wat voor soort :name heeft u",
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
                            'icon' => 'icon-apartment-ground-floor-corner',
                        ],
                        7 => [
                            'icon' => 'icon-apartment-ground-floor-between',
                        ],
                        8 => [
                            'icon' => 'icon-upstairs-apartment-corner',
                        ],
                        9 => [
                            'icon' => 'icon-upstairs-apartment-between',
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
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:roof_types,id'],
                'save_in' => 'building_features.roof_type_id',
                'short' => 'roof-type',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.roof-type',
                'tool_question_values' => $roofTypes,
                'extra' => [
                    'column' => 'short',
                    'data' => [
                        'pitched' => [
                            'icon' => 'icon-pitched-roof',
                        ],
                        'flat' => [
                            'icon' => 'icon-flat-roof',
                        ],
                        // TODO: For later, when new logic is applied for roof types
//                                    'flat-pitched-roof'  => [
//                                        'icon' => 'icon-flat-pitched-roof'
//                                    ],
                        'gabled-roof' => [
                            'icon' => 'icon-pointed-roof'
                        ],
                        'rounded-roof' => [
                            'icon' => 'icon-rounded-roof'
                        ],
                        'straw-roof' => [
                            // todo: add rieten dak
                            'icon' => 'icon-pointed-roof'
                        ],
                        'none' => [
                            'icon' => 'icon-not-relevant',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::INT,
                // TODO: Date is static, should be dynamic... (counts for other questions too)
                'validation' => ['required', 'numeric', 'integer', 'between:1000,' . date('Y')],
                'save_in' => 'building_features.build_year',
                'short' => 'build-year',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.build-year',
                'options' => ['min' => 1000, 'max' => date('Y'), 'value' => 1930, 'step' => 1],
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['numeric', 'integer', 'between:1,5'],
                'save_in' => 'building_features.building_layers',
                'short' => 'building-layers',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.building-layers',
                'options' => ['min' => 1, 'max' => 6, 'value' => 3, 'step' => 1],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:example_buildings,id'],
                'short' => 'specific-example-building',
                'save_in' => 'building_features.example_building_id',
                'translation' => 'Welke woning lijkt het meest op jouw woning?',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['numeric', 'in:1,2,0', 'exists:tool_question_custom_values,short'],
                'save_in' => 'building_features.monument',
                'short' => 'monument',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.monument',
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
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['numeric', 'exists:energy_labels,id'],
                'save_in' => 'building_features.energy_label_id',
                'short' => 'energy-label',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.energy-label',
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
                        '?' => [
                            'icon' => 'icon-label-unknown',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::FLOAT,
                'validation' => ['required', 'numeric', 'min:20', 'max:999999'],
                'save_in' => 'building_features.surface',
                'short' => 'surface',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface',
                'unit_of_measure' => __('general.unit.square-meters.title'),
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepBuildingData->id}.comment",
                'for_specific_input_source_id' => $residentInputSource->id,
                'short' => 'building-data-comment-resident',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.comment',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepBuildingData->id}.comment",
                'for_specific_input_source_id' => $coachInputSource->id,
                'short' => 'building-data-comment-coach',
                'translation' => 'cooperation/tool/general-data/building-characteristics.index.comment',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'save_in' => 'user_energy_habits.resident_count',
                'short' => 'resident-count',
                'translation' => 'Hoeveel mensen wonen er in de woning?',
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
                    8 => [
                        'name' => 'Acht',
                        'extra' => [
                            'icon' => 'icon-persons-more-than-seven',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['required', 'numeric', 'min:10', 'max:30', 'gte:thermostat_low'],
                'save_in' => 'user_energy_habits.thermostat_high',
                'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-high',
                'short' => 'thermostat-high',
                'options' => ['min' => 10, 'max' => 30, 'value' => 20, 'step' => 1],
                'unit_of_measure' => '°',
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                'save_in' => 'user_energy_habits.thermostat_low',
                'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-low',
                'short' => 'thermostat-low',
                'options' => ['min' => 10, 'max' => 30, 'value' => 16, 'step' => 1],
                'unit_of_measure' => '°',
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['required', 'numeric', 'between:1,24'],
                'save_in' => 'user_energy_habits.hours_high',
                'short' => 'hours-high',
                'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.hours-high',
                'options' => ['min' => 0, 'max' => 24, 'value' => 12, 'step' => 1],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:building_heatings,id'],
                'save_in' => 'user_energy_habits.heating_first_floor',
                'short' => 'heating-first-floor',
                // was cooperation/tool/general-data/usage.index.heating-habits.heating-first-floor
                'translation' => 'Wat is de situatie op de eerste verdieping?',
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
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:building_heatings,id'],
                'save_in' => 'user_energy_habits.heating_second_floor',
                'short' => 'heating-second-floor',
                // was cooperation/tool/general-data/usage.index.heating-habits.heating-second-floor
                'translation' => 'Wat is de situatie op de tweede verdieping?',
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
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:comfort_level_tap_waters,id'],
                'save_in' => 'user_energy_habits.water_comfort_id',
                'short' => 'water-comfort',
                'translation' => 'Wat is het comfortniveau voor het gebruik van warm tapwater?',
                'tool_question_values' => $comfortLevelsTapWater,
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'cook-type',
                'translation' => "Hoe wordt er gekookt?",
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
                'data_type' => Caster::INT,
                'validation' => ['required', 'numeric', 'integer', 'min:0', 'max:10000'],
                'save_in' => 'user_energy_habits.amount_gas',
                'short' => 'amount-gas',
                'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.gas-usage',
                'unit_of_measure' => __('general.unit.cubic-meters.title'),
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['required', 'numeric', 'integer', 'min:-10000', 'max:25000'],
                'save_in' => 'user_energy_habits.amount_electricity',
                'short' => 'amount-electricity',
                'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.amount-electricity',
                'unit_of_measure' => 'kWh'
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepUsageQuickScan->id}.comment",
                'for_specific_input_source_id' => $residentInputSource->id,
                'short' => 'usage-quick-scan-comment-resident',
                'translation' => 'cooperation/tool/general-data/usage.index.comment',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepUsageQuickScan->id}.comment",
                'for_specific_input_source_id' => $coachInputSource->id,
                'short' => 'usage-quick-scan-comment-coach',
                'translation' => 'cooperation/tool/general-data/usage.index.comment',
            ],
            [
                'data_type' => Caster::INT,
                'short' => 'remaining-living-years',
                'validation' => ['required', 'numeric', 'min:1', 'max:10'],
                'translation' => 'Hoeveel jaar denkt u hier nog te blijven wonen?',
                'options' => [
                    'min' => 1, 'max' => 10,
                    'max_label' => 'cooperation/frontend/tool.form.questions.values.more-than',
                    'value' => 7, 'step' => 1,
                ],
            ],
            [
                'data_type' => Caster::JSON,
                'validation' => ['required', 'in:1,2,3,4,5'],
                'short' => 'comfort-priority',
                'translation' => "Welke zaken vindt u belangrijk?",
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
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepLivingRequirements->id}.comment",
                'for_specific_input_source_id' => $residentInputSource->id,
                'short' => 'living-requirements-comment-resident',
                'translation' => 'cooperation/tool/general-data/interest.index.comment',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepLivingRequirements->id}.comment",
                'for_specific_input_source_id' => $coachInputSource->id,
                'short' => 'living-requirements-comment-coach',
                'translation' => 'cooperation/tool/general-data/interest.index.comment',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_elements.{$wallInsulation->id}.element_value_id",
                'short' => 'current-wall-insulation',
                'translation' => "Wat is de staat van de muurisolatie?",
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
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_elements.{$floorInsulation->id}.element_value_id",
                'short' => 'current-floor-insulation',
                'translation' => "Wat is de staat van de vloerisolatie?",
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
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_elements.{$roofInsulation->id}.element_value_id",
                'short' => 'current-roof-insulation',
                'translation' => "Wat is de staat van de dakisolatie?",
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
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_elements.{$livingRoomsWindows->id}.element_value_id",
                'short' => 'current-living-rooms-windows',
                'translation' => "Welke glassoort heeft u in de leefruimtes?",
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
                            'icon' => 'icon-glass-hr-tp',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_elements.{$sleepingRoomsWindows->id}.element_value_id",
                'short' => 'current-sleeping-rooms-windows',
                'translation' => "Welke glassoort heeft u in de slaapruimtes?",
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
                            'icon' => 'icon-glass-hr-tp',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'heat-source',
                'translation' => "Wat wordt er gebruikt voor verwarming",
                'options' => ['value' => ['hr-boiler'],],
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
                        'name' => 'Warmtepanelen / Infrarood',
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
                    'sun-boiler' => [
                        'name' => 'Zonneboiler',
                        'extra' => [
                            'icon' => 'icon-sun-boiler-heating',
                        ],
                    ],
                    'none' => [
                        'name' => 'Anders...',
                        'extra' => [
                            'icon' => 'icon-other',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['required', 'string'],
                'short' => 'heat-source-other',
                'translation' => 'Wat wordt er dan gebruikt voor verwarming?',
            ],
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'heat-source-warm-tap-water',
                'translation' => "Wat word er gebruikt voor warm tapwater",
                'options' => ['value' => ['hr-boiler'],],
                'tool_question_custom_values' => [
                    'hr-boiler' => [
                        'name' => 'Gasketel',
                        'extra' => [
                            'icon' => 'icon-central-heater-gas',
                        ],
                    ],
                    'kitchen-geyser' => [
                        'name' => 'Bad/keukengeiser',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'electric-boiler' => [
                        'name' => 'Elektrische boiler',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'heat-pump-boiler' => [
                        'name' => 'Warmtepomp boiler',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'heat-pump' => [
                        'name' => 'Warmtepomp',
                        'extra' => [
                            'icon' => 'icon-heat-pump',
                        ],
                        'conditions' => [
                            [
                                [
                                    'column' => 'heat-source',
                                    'operator' => Clause::CONTAINS,
                                    'value' => 'heat-pump',
                                ],
                            ],
                        ],
                    ],
                    'sun-boiler' => [
                        'name' => 'Zonneboiler',
                        'extra' => [
                            'icon' => 'icon-sun-boiler-heating',
                        ],
                    ],
                    'district-heating' => [
                        'name' => 'Stadsverwarming',
                        'extra' => [
                            'icon' => 'icon-district-heating',
                        ],
                    ],
                    'none' => [
                        'name' => 'Anders...',
                        'extra' => [
                            'icon' => 'icon-other',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['required', 'string'],
                'short' => 'heat-source-warm-tap-water-other',
                'translation' => 'Wat wordt er dan gebruikt voor warm tapwater?',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_services.{$boiler->id}.service_value_id",
                'short' => 'boiler-type',
                'translation' => "Wat voor gasketel heeft u?",
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
                'data_type' => Caster::INT,
                'validation' => ['nullable', 'numeric', 'integer', 'between:1970,' . date('Y')],
                'save_in' => "building_services.{$boiler->id}.extra.date",
                'short' => 'boiler-placed-date',
                'translation' => "Wanneer is de gasketel geplaatst?",
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_services.{$heatPump->id}.service_value_id",
                'short' => 'heat-pump-type',
                'translation' => "Wat voor type warmtepomp is er?",
                'tool_question_values' => $heatPump->values()->orderBy('order')->get(),
                'extra' => [
                    'column' => 'calculate_value',
                    'data' => [
                        1 => [],
                        2 => [],
                        3 => [],
                        4 => [],
                        5 => [],
                        6 => [],
                    ],
                ],
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['nullable', 'numeric', 'integer', 'between:1970,' . date('Y')],
                'short' => 'heat-pump-placed-date',
                'translation' => 'Wanneer is de warmtepomp geplaatst?',
            ],
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'building-heating-application',
                'translation' => "Hoe is de verwarming?",
                'tool_question_custom_values' => [
                    'radiators' => [
                        'name' => 'Radiator',
                        'extra' => [
                            'icon' => 'icon-radiator',
                        ],
                    ],
                    'floor-heating' => [
                        'name' => 'Vloerverwarming',
                        'extra' => [
                            'icon' => 'icon-floor-heating',
                        ],
                    ],
                    'wall-heating' => [
                        'name' => 'Wandverwarming',
                        'extra' => [
                            'icon' => 'icon-wall-heating',
                        ],
                    ],
                    'air-heating' => [
                        'name' => 'Hete lucht ',
                        'extra' => [
                            'icon' => 'icon-air-conditioning-hot',
                        ],
                    ],
                    'low-temperature-heater' => [
                        'name' => 'Lage temp. radiatoren',
                        'extra' => [
                            'icon' => 'icon-radiator-low-temp',
                        ],
                    ],
                    'none' => [
                        'name' => 'Anders...',
                        'extra' => [
                            'icon' => 'icon-other',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['required', 'string'],
                'short' => 'building-heating-application-other',
                'translation' => 'Wat voor andere verwarming is er nu?',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'fifty-degree-test',
                'translation' => "Heb je de 50 graden test gedaan?",
                'options' => ['value' => 'no'],
                'tool_question_custom_values' => [
                    'yes' => [
                        'name' => 'Ja',
                    ],
                    'no' => [
                        'name' => 'Nee',
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'boiler-setting-comfort-heat',
                'translation' => "Hoe moet de CV ketel ingesteld zijn om het huis comfortabel te kunnen verwarmen?",
                'options' => ['value' => 'temp-high'],
                'tool_question_custom_values' => [
                    'temp-high' => [
                        'name' => 'Op hoge temperatuur',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'temp-50' => [
                        'name' => 'Op 50 graden',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'temp-low' => [
                        'name' => 'Op lage temperatuur',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'unsure' => [
                        'name' => 'Weet ik niet',
                        'extra' => [
                            'icon' => 'icon-other',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                'save_in' => "building_services.{$ventilation->id}.service_value_id",
                'short' => 'ventilation-type',
                // was current-state -> hoe word het huis geventileerd
                'translation' => "Hoe wordt uw woning nu geventileerd?",
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
            [
                'data_type' => Caster::BOOL,
                'validation' => ['required', 'boolean'],
                'save_in' => "building_services.{$ventilation->id}.extra.demand_driven",
                'short' => 'ventilation-demand-driven',
                // was current-state -> Vraaggestuurde regeling
                'translation' => "Heeft u vraaggestuurde regeling?",
                'tool_question_custom_values' => [
                    true => [
                        'name' => __('woningdossier.cooperation.radiobutton.yes'),
                        'extra' => [],
                    ],
                    false => [
                        'name' => __('woningdossier.cooperation.radiobutton.no'),
                        'extra' => [],
                    ],
                ],
            ],
            [
                'data_type' => Caster::BOOL,
                'save_in' => "building_services.{$ventilation->id}.extra.heat_recovery",
                'validation' => ['required', 'boolean'],
                'short' => 'ventilation-heat-recovery',
                // was current-state -> Met warmte terugwinning
                'translation' => "Heeft u warmte terugwinning?",
                'tool_question_custom_values' => [
                    true => [
                        'name' => __('woningdossier.cooperation.radiobutton.yes'),
                        'extra' => [],
                    ],
                    false => [
                        'name' => __('woningdossier.cooperation.radiobutton.no'),
                        'extra' => [],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', "exists:element_values,id",],
                'save_in' => "building_elements.{$crackSealing->id}.element_value_id",
                'short' => 'crack-sealing-type',
                // was current-state -> zijn de ramen en deuren voorzien van kierdichting
                'translation' => "Heeft u kierdichting?",
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
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'has-solar-panels',
                'translation' => "Heeft u zonnepanelen?",
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
                'data_type' => Caster::INT,
                'validation' => ["required_if:has_solar_panels,yes", 'numeric', 'integer', 'min:1', 'max:50'],
                'save_in' => "building_services.{$solarPanels->id}.extra.value",
                'short' => 'solar-panel-count',
                // was current-state -> hoeveel zonnepanelen zijn er aanwezig
                'translation' => "Hoeveel zonnepanelen zijn er aanwezig?",
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ["required_if:has_solar_panels,yes", 'numeric', 'integer', 'min:1'],
                'save_in' => "building_pv_panels.total_installed_power",
                'short' => 'total-installed-power',
                // was current-state -> Geinstalleerd vermogen (totaal)
                'translation' => "Wat is het totale vermogen van de geplaatste panelen?",
                'unit_of_measure' => 'WP',
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ['nullable', 'numeric', 'integer', 'between:1900,' . date('Y')],
                'save_in' => "building_services.{$solarPanels->id}.extra.year",
                'short' => 'solar-panels-placed-date',
                // was current-state -> Geinstalleerd vermogen (totaal)
                'translation' => "Wanneer zijn de zonnepanelen geplaatst?",
                'placeholder' => 'Voer een jaartal in',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'interested-in-heat-pump',
                'translation' => "Overweeg je om een warmtepomp te nemen?",
                'options' => ['value' => 'no'],
                'tool_question_custom_values' => [
                    'yes' => [
                        'name' => 'Ja',
                        'extra' => [
                            'icon' => 'icon-heat-pump',
                        ],
                    ],
                    'no' => [
                        'name' => 'Nee',
                        'extra' => [
                            'icon' => 'icon-heat-pump-none',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'interested-in-heat-pump-variant',
                'translation' => "Weet je al welk soort warmtepomp je zou willen?",
                'tool_question_custom_values' => [
                    'unsure' => [
                        'name' => 'Geef mij advies',
                        'extra' => [
                            'icon' => 'icon-other',
                        ],
                    ],
                    'hybrid-heat-pump' => [
                        'name' => 'Ja, een hybride warmtepomp',
                        'extra' => [
                            'icon' => 'icon-heat-pump-hybrid',
                        ],
                    ],
                    'full-heat-pump' => [
                        'name' => 'Ja, een volledige warmtepomp',
                        'extra' => [
                            'icon' => 'icon-heat-pump',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepResidentialStatus->id}_element.comment",
                'for_specific_input_source_id' => $residentInputSource->id,
                'short' => 'residential-status-element-comment-resident',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.element',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepResidentialStatus->id}_element.comment",
                'for_specific_input_source_id' => $coachInputSource->id,
                'short' => 'residential-status-element-comment-coach',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.element',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepResidentialStatus->id}_service.comment",
                'for_specific_input_source_id' => $residentInputSource->id,
                'short' => 'residential-status-service-comment-resident',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.service',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$stepResidentialStatus->id}_service.comment",
                'for_specific_input_source_id' => $coachInputSource->id,
                'short' => 'residential-status-service-comment-coach',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.service',
            ],
            #-------------------------
            # Expert scan questions only
            #-------------------------
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['nullable', 'exists:tool_question_custom_values,short'],
                'short' => 'heat-source-considerable',
                'translation' => 'Welke maatregelen wil je meenemen in de berekening?',
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
                    'sun-boiler' => [
                        'name' => 'Zonneboiler',
                        'extra' => [
                            'icon' => 'icon-sun-boiler-hot-water',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:comfort_level_tap_waters,id'],
                'short' => 'new-water-comfort',
                'translation' => 'Wat wordt het comfortniveau voor het gebruik van warm tapwater?',
                'tool_question_custom_values' => [
                    'standard' => [
                        'name' => 'Standaard',
                        'extra' => [
                            'calculate_value' => '1',
                        ],
                    ],
                    'comfortable' => [
                        'name' => 'Comfortabel',
                        'extra' => [
                            'calculate_value' => '2',
                        ],
                    ],
                    'extra-comfortable' => [
                        'name' => 'Extra comfortabel',
                        'extra' => [
                            'calculate_value' => '3',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'new-heat-source',
                'translation' => "Wat er komen voor verwarming?",
                'options' => ['value' => ['hr-boiler'],],
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
                        'name' => 'Warmtepanelen / Infrarood',
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
                    'sun-boiler' => [
                        'name' => 'Zonneboiler',
                        'extra' => [
                            'icon' => 'icon-sun-boiler-heating',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'new-heat-source-warm-tap-water',
                'translation' => "Wat moet er komen voor warm tapwater?",
                'options' => ['value' => ['hr-boiler'],],
                'tool_question_custom_values' => [
                    'hr-boiler' => [
                        'name' => 'Gasketel',
                        'extra' => [
                            'icon' => 'icon-central-heater-gas',
                        ],
                    ],
                    'kitchen-geyser' => [
                        'name' => 'Bad/keukengeiser',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'electric-boiler' => [
                        'name' => 'Elektrische boiler',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'heat-pump-boiler' => [
                        'name' => 'Warmtepomp boiler',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'heat-pump' => [
                        'name' => 'Warmtepomp',
                        'extra' => [
                            'icon' => 'icon-heat-pump',
                        ],
                        'conditions' => [
                            [
                                [
                                    'column' => 'new-heat-source',
                                    'operator' => Clause::CONTAINS,
                                    'value' => 'heat-pump',
                                ],
                            ],
                        ],
                    ],
                    'sun-boiler' => [
                        'name' => 'Zonneboiler',
                        'extra' => [
                            'icon' => 'icon-sun-boiler-heating',
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
            [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'new-building-heating-application',
                'translation' => "Hoe is de verwarming in de nieuwe situatie?",
                'tool_question_custom_values' => [
                    'radiators' => [
                        'name' => 'Radiator',
                        'extra' => [
                            'icon' => 'icon-radiator',
                        ],
                    ],
                    'floor-heating' => [
                        'name' => 'Vloerverwarming',
                        'extra' => [
                            'icon' => 'icon-floor-heating',
                        ],
                    ],
                    'wall-heating' => [
                        'name' => 'Wandverwarming',
                        'extra' => [
                            'icon' => 'icon-wall-heating',
                        ],
                    ],
                    'air-heating' => [
                        'name' => 'Hete lucht ',
                        'extra' => [
                            'icon' => 'icon-air-conditioning-hot',
                        ],
                    ],
                    'low-temperature-heater' => [
                        'name' => 'Lage temp. radiatoren',
                        'extra' => [
                            'icon' => 'icon-radiator-low-temp',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                //'save_in' => "building_services.{$boiler->id}.service_value_id",
                'short' => 'new-boiler-type',
                'translation' => "Wat is het type van de nieuwe ketel?",
                'tool_question_custom_values' => [
                    'conventional' => [
                        'name' => 'Conventioneel rendement ketel',
                        'extra' => [
                            'calculate_value' => '1',
                        ],
                    ],
                    'improved-efficiency' => [
                        'name' => 'verbeterd rendement ketel',
                        'extra' => [
                            'calculate_value' => '2',
                        ],
                    ],
                    'hr100' => [
                        'name' => 'HR100 ketel',
                        'extra' => [
                            'calculate_value' => '3',
                        ],
                    ],
                    'hr104' => [
                        'name' => 'HR104 ketel',
                        'extra' => [
                            'calculate_value' => '4',
                        ],
                    ],
                    'hr107' => [
                        'name' => 'HR107 ketel',
                        'extra' => [
                            'calculate_value' => '5',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'new-boiler-setting-comfort-heat',
                'translation' => "Wat wordt de stooktemperatuur in de nieuwe situatie?",
                'tool_question_custom_values' => [
                    'temp-high' => [
                        'name' => 'Op hoge temperatuur',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'temp-50' => [
                        'name' => 'Op 50 graden',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                    'temp-low' => [
                        'name' => 'Op lage temperatuur',
                        'extra' => [
                            'icon' => 'icon-placeholder',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
                'short' => 'new-cook-type',
                'translation' => "Hoe wordt er gekookt in de nieuwe situatie?",
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
                'data_type' => Caster::IDENTIFIER,
                'validation' => ['required', 'exists:element_values,id'],
                //'save_in' => "building_services.{$heatPump->id}.service_value_id",
                'short' => 'new-heat-pump-type',
                'translation' => "Welke soort warmtepomp moet er komen?",
                'tool_question_custom_values' => [
                    'hybrid-heat-pump-outside-air' => [
                        'name' => 'Hybride warmtepomp met buitenlucht',
                        'extra' => [
                            'calculate_value' => '1',
                        ],
                    ],
                    'hybrid-heat-pump-ventilation-air' => [
                        'name' => 'Hybride warmtepomp met ventilatielucht',
                        'extra' => [
                            'calculate_value' => '2',
                        ],
                    ],
                    'hybrid-heat-pump-pvt-panels' => [
                        'name' => 'Hybride warmtepomp met pvt panelen',
                        'extra' => [
                            'calculate_value' => '3',
                        ],
                    ],
                    'full-heat-pump-outside-air' => [
                        'name' => 'Volledige warmtepomp met buitenlucht',
                        'extra' => [
                            'calculate_value' => '4',
                        ],
                    ],
                    'full-heat-pump-ground-heat' => [
                        'name' => 'Volledige warmtepomp met bodemwarmte',
                        'extra' => [
                            'calculate_value' => '5',
                        ],
                    ],
                    'full-heat-pump-pvt-panels' => [
                        'name' => 'Volledige warmtepomp met pvt panelen',
                        'extra' => [
                            'calculate_value' => '6',
                        ],
                    ],
                ],
            ],
            [
                'data_type' => Caster::INT,
                'validation' => ["required", 'numeric', 'min:1'],
                'short' => 'heat-pump-preferred-power',
                'translation' => "Gewenst vermogen van de warmtepomp",
                'unit_of_measure' => 'KW',
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ["required", 'exists:tool_question_custom_values,short'],
                'short' => 'outside-unit-space',
                'translation' => "Is er voldoende ruimte voor een buitenunit?",
                'tool_question_custom_values' => [
                    'yes' => [
                        'name' => 'Ja',
                    ],
                    'no' => [
                        'name' => 'Nee',
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ["required", 'exists:tool_question_custom_values,short'],
                'short' => 'inside-unit-space',
                'translation' => "Is er voldoende ruimte voor een binnenunit?",
                'tool_question_custom_values' => [
                    'yes' => [
                        'name' => 'Ja',
                    ],
                    'no' => [
                        'name' => 'Nee',
                    ],
                ],
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ["required", 'exists:pv_panel_orientations,id'],
                'save_in' => 'building_heaters.pv_panel_orientation_id',
                'short' => 'heater-pv-panel-orientation',
                'translation' => "Oriëntatie van de collector",
                'help_text' => "Geef hier aan in welke oriëntatie de zonnecollector geplaatst wordt.",
                'tool_question_values' => $collectorOrientations,
            ],
            [
                'data_type' => Caster::IDENTIFIER,
                'validation' => ["required", 'exists:tool_question_custom_values,short'],
                'save_in' => 'building_heaters.angle',
                'short' => 'heater-pv-panel-angle',
                'unit_of_measure' => '°',
                'translation' => "Hellingshoek van de collector",
                'help_text' => "Geef hier aan onder welke hellingshoek de zonnecollector geplaatst wordt. Op een hellend dak is de hellingshoek van de collector meestal gelijk aan de dakhelling.",
                'tool_question_custom_values' => collect(\App\Helpers\KeyFigures\Heater\KeyFigures::getAngles())
                    ->map(fn ($angle) => ['name' => $angle]),
            ],
            // TODO: How to handle if we delete these steps?
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$hrBoilerStep->id}.comment",
                'short' => 'hr-boiler-comment',
                'translation' => 'Toelichting op de CV ketel',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$heatPumpStep->id}.comment",
                'short' => 'heat-pump-comment',
                'translation' => 'Toelichting op de warmtepomp',
            ],
            [
                'data_type' => Caster::STRING,
                'validation' => ['nullable', 'string'],
                'save_in' => "step_comments.{$sunBoilerStep->id}.comment",
                'short' => 'sun-boiler-comment',
                'translation' => 'Toelichting op de zonneboiler',
            ],
        ];


        foreach ($questions as $questionData) {
            // Create the question itself

            // Translation can be a key or text. We compare the results, because if it's a key, then the
            // result will be different
            $translation = $questionData['translation'];
            $questionData['name'] = [
                'nl' => \App\Helpers\Translation::hasTranslation($translation . '.title')
                    ? __($translation . '.title') : $translation,
            ];
            $helpTextTranslation = $questionData['help_text'] ?? $translation;
            $questionData['help_text'] = [
                'nl' => \App\Helpers\Translation::hasTranslation($helpTextTranslation . '.help')
                    ? __($helpTextTranslation . '.help') : $helpTextTranslation,
            ];

            $insertData = Arr::except($questionData,
                ['tool_question_values', 'tool_question_custom_values', 'extra', 'translation']);

            // Encode data for DB insert...
            $insertData['name'] = json_encode($insertData['name']);
            $insertData['help_text'] = json_encode($insertData['help_text']);
            $insertData['placeholder'] = empty($insertData['placeholder']) ? null : json_encode(['nl' => $insertData['placeholder']]);
            $insertData['options'] = empty($insertData['options']) ? null : json_encode($insertData['options']);
            $insertData['validation'] = empty($insertData['validation']) ? null : json_encode($insertData['validation']);

            $toolQuestion = ToolQuestion::where('short', $questionData['short'])
                ->first();

            // We check if it exists already. Admins can change question names and help texts. We don't
            // want to override that
            if ($toolQuestion instanceof ToolQuestion) {
                $insertData['name'] = json_encode($toolQuestion->getTranslations('name'));
                $insertData['help_text'] = json_encode($toolQuestion->getTranslations('help_text'));
            }

            // We can updateOrInsert this!
            DB::table('tool_questions')->updateOrInsert([
                'short' => $questionData['short'],
            ], $insertData);

            $toolQuestion = ToolQuestion::where('short', $questionData['short'])
                ->first();


            if (isset($questionData['tool_question_custom_values'])) {
                $toolQuestionCustomValueOrder = 0;
                foreach ($questionData['tool_question_custom_values'] as $short => $customValueData) {
                    $name = $customValueData['name'];
                    $extra = $customValueData['extra'] ?? [];
                    $conditions = $customValueData['conditions'] ?? [];

                    $insertData = [
                        'tool_question_id' => $toolQuestion->id,
                        'order' => $toolQuestionCustomValueOrder,
                        'show' => true,
                        // so we will compare the short to determine what is what, but we will keep value for now
                        'short' => $short,
                        'name' => json_encode([
                            'nl' => $name,
                        ]),
                        'extra' => json_encode($extra),
                        'conditions' => json_encode($conditions),
                    ];

                    DB::table('tool_question_custom_values')->updateOrInsert([
                        'short' => $short,
                        'tool_question_id' => $toolQuestion->id,
                    ], $insertData);

                    $toolQuestionCustomValueOrder++;
                }
            }

            if (isset($questionData['tool_question_values'])) {
                $extra = $questionData['extra'] ?? [];

                foreach ($questionData['tool_question_values'] as $toolQuestionValueOrder => $toolQuestionValue) {
                    if (isset($extra['column'])) {
                        // TODO: Not relevant for now but when needed we could put the conditions inside the $extraData
                        $extraData = $extra['data'][$toolQuestionValue->{$extra['column']}];
                    }

                    $insertData = [
                        'tool_question_id' => $toolQuestion->id,
                        'order' => $toolQuestionValueOrder,
                        'show' => true,
                        'tool_question_valuable_type' => get_class($toolQuestionValue),
                        'tool_question_valuable_id' => $toolQuestionValue->id,
                        // We grab the extra data by the set column (e.g. calculate_value)
                        'extra' => json_encode(($extraData ?? $extra)),
                    ];

                    DB::table('tool_question_valuables')->updateOrInsert([
                        'order' => $toolQuestionValueOrder,
                        'tool_question_id' => $toolQuestion->id,
                    ], $insertData);
                }
            }
        }
    }
}