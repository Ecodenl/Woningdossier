<?php

namespace Database\Seeders;

use App\Helpers\Conditions\Clause;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\ToolCalculationResult;
use App\Models\ToolLabel;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubSteppablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Templates
        $templateDefault = SubStepTemplate::findByShort('template-default');
        $template2rows1top2bottom = SubStepTemplate::findByShort('template-2-rows-1-top-2-bottom');
        $template2rows3top1bottom = SubStepTemplate::findByShort('template-2-rows-3-top-1-bottom');
        $templateCustomChanges = SubStepTemplate::findByShort('template-custom-changes');
        $templateSummary = SubStepTemplate::findByShort('template-summary');

        // Some services for conditions
        $heatPump = Service::findByShort('heat-pump');
        $ventilation = Service::findByShort('house-ventilation');

        // Tool question types
        $checkboxIconType = ToolQuestionType::findByShort('checkbox-icon');
        $radioIconType = ToolQuestionType::findByShort('radio-icon');
        $radioIconSmallType = ToolQuestionType::findByShort('radio-icon-small');
        $radioType = ToolQuestionType::findByShort('radio');
        $textType = ToolQuestionType::findByShort('text');
        $sliderType = ToolQuestionType::findByShort('slider');
        $textareaType = ToolQuestionType::findByShort('textarea');
        $textareaPopupType = ToolQuestionType::findByShort('textarea-popup');
        $measurePriorityType = ToolQuestionType::findByShort('rating-slider');
        $dropdownType = ToolQuestionType::findByShort('dropdown');
        $multiDropdownType = ToolQuestionType::findByShort('multi-dropdown');

        // Conditional required values
        $wallInsulationUnknown = Element::findByShort('wall-insulation')->values()->where('calculate_value', 1)->first();
        $wallInsulationNone = Element::findByShort('wall-insulation')->values()->where('calculate_value', 2)->first();

        $floorInsulationUnknown = Element::findByShort('floor-insulation')->values()->where('calculate_value', 1)->first();
        $floorInsulationNone = Element::findByShort('floor-insulation')->values()->where('calculate_value', 2)->first();

        $roofInsulationUnknown = Element::findByShort('roof-insulation')->values()->where('calculate_value', 1)->first();
        $roofInsulationNone = Element::findByShort('roof-insulation')->values()->where('calculate_value', 2)->first();
        $pitchedRoof = RoofType::findByShort('pitched');
        $flatRoof = RoofType::findByShort('flat');

        // Measure applications
        $floorInsulation = MeasureApplication::findByShort('floor-insulation');
        $bottomInsulation = MeasureApplication::findByShort('bottom-insulation');
        $floorInsulationResearch = MeasureApplication::findByShort('floor-insulation-research');

        $cavityWallInsulation = MeasureApplication::findByShort('cavity-wall-insulation');
        $facadeWallInsulation = MeasureApplication::findByShort('facade-wall-insulation');
        $wallInsulationResearch = MeasureApplication::findByShort('wall-insulation-research');

        $glassInLead = MeasureApplication::findByShort('glass-in-lead');
        $hrppGlassOnly = MeasureApplication::findByShort('hrpp-glass-only');
        $hrppGlassFrames = MeasureApplication::findByShort('hrpp-glass-frames');
        $hr3pFrames = MeasureApplication::findByShort('hr3p-frames');

        $roofInsulationPitchedInside = MeasureApplication::findByShort('roof-insulation-pitched-inside');
        $roofInsulationPitchedReplaceTiles = MeasureApplication::findByShort('roof-insulation-pitched-replace-tiles');
        $roofInsulationFlatCurrent = MeasureApplication::findByShort('roof-insulation-flat-current');
        $roofInsulationFlatReplaceCurrent = MeasureApplication::findByShort('roof-insulation-flat-replace-current');

        $hrBoilerReplace = MeasureApplication::findByShort('high-efficiency-boiler-replace');
        $heaterPlaceReplace = MeasureApplication::findByShort('heater-place-replace');
        $solarPanelsPlaceReplace = MeasureApplication::findByShort('solar-panels-place-replace');

        $crackSealingMeasureApplication = MeasureApplication::findByShort('crack-sealing');
        $ventilationBalancedWtw = MeasureApplication::findByShort('ventilation-balanced-wtw');
        $ventilationDecentralWtw = MeasureApplication::findByShort('ventilation-decentral-wtw');
        $ventilationDemandDriven = MeasureApplication::findByShort('ventilation-demand-driven');

        $hybridHeatPumpOutsideAir = MeasureApplication::findByShort('hybrid-heat-pump-outside-air');
        $hybridHeatPumpVentilationAir = MeasureApplication::findByShort('hybrid-heat-pump-ventilation-air');
        $hybridHeatPumpPvtPanels = MeasureApplication::findByShort('hybrid-heat-pump-pvt-panels');
        $fullHeatPumpOutsideAir = MeasureApplication::findByShort('full-heat-pump-outside-air');
        $fullHeatPumpGroundHeat = MeasureApplication::findByShort('full-heat-pump-ground-heat');
        $fullHeatPumpPvtPanels = MeasureApplication::findByShort('full-heat-pump-pvt-panels');

        // Needed conditional elements
        $crawlSpace = Element::findByShort('crawlspace');
        $crawlSpaceHigh = $crawlSpace->elementValues()->where('calculate_value', 45)->first();
        $crawlSpaceMid = $crawlSpace->elementValues()->where('calculate_value', 30)->first();

        #-------------------------
        # Quick Scan SubSteppables
        #-------------------------
        $this->saveStructure([
            'building-data' => [
                // sub step name
                'Woning type' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-type-category'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Wat voor woning' => [
                    'order' => 1,
                    'conditions' => [
                        [
                            [
                                'column' => 'fn',
                                'operator' => 'BuildingType',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Huur of koop' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-contract-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Wat voor dak' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('roof-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Bouwjaar en oppervlak' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('build-year'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-layers'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Specifieke voorbeeld woning' => [
                    'order' => 4,
                    'conditions' => [
                        [
                            [
                                'column' => 'fn',
                                'operator' => 'SpecificExampleBuilding',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('specific-example-building'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Monument en energielabel' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('monument'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('energy-label'),
                            'tool_question_type_id' => $radioIconSmallType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Gebruikersoppervlak en bijzonderheden' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('surface'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Samenvatting woninggegevens' => [
                    'order' => 8,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-data-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-data-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'usage-quick-scan' => [
                'Hoeveel bewoners' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('resident-count'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Thermostaat gebruik' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('thermostat-high'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('thermostat-low'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hours-high'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Gebruik warm tapwater' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heating-first-floor'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heating-second-floor'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-comfort'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Gas en elektra gebruik' => [
                    'order' => 3,
                    'sub_step_template_id' => $template2rows1top2bottom->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('cook-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-gas'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-electricity'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Samenvatting bewoners-gebruik' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('usage-quick-scan-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('usage-quick-scan-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'living-requirements' => [
                'Hoelang blijven wonen' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('remaining-living-years'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Welke zaken vindt u belangrijk?' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('comfort-priority'),
                            'tool_question_type_id' => $measurePriorityType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Welke zaken vervangen' => [
                    'order' => 2,
                    // note: dit is een custom vraag, zie CustomChanges
                    'sub_step_template_id' => $templateCustomChanges->id,
                ],
                'Samenvatting woonwensen' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'residential-status' => [
                'Muurisolatie' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('current-wall-insulation'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Vloerisolatie' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('current-floor-insulation'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Dakisolatie' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('current-roof-insulation'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Glasisolatie eerste woonlaag' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('current-living-rooms-windows'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Glasisolatie tweede woonlaag' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('current-sleeping-rooms-windows'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Verwarming' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-source'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'Verwarming warm water' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'Gasketel vragen' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::CONTAINS,
                                'value' => 'hr-boiler',
                            ],
                        ],
                        [
                            [
                                'column' => 'heat-source-warm-tap-water',
                                'operator' => Clause::CONTAINS,
                                'value' => 'hr-boiler',
                            ],
                        ],
                    ],
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('boiler-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Warmtepomp' => [
                    'order' => 8,
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::CONTAINS,
                                'value' => 'heat-pump',
                            ],
                        ],
                    ],
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Hoe is de verwarming' => [
                    'order' => 9,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                '50 graden test' => [
                    'order' => 10,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('fifty-degree-test'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                    [
                                        [
                                            'column' => 'building-heating-application',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'radiators',
                                        ],
                                        [
                                            'column' => 'building-heating-application',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'air-heating',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-setting-comfort-heat'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Ventilatie' => [
                    'order' => 11,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-demand-driven'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 1)->first()->id, // Natuurlijke ventilatie
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-heat-recovery'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 1)->first()->id, // Natuurlijke ventilatie
                                    ],
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 2)->first()->id, // Mechanische ventilatie
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                'Kierdichting' => [
                    'order' => 12,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Zonnepanelen' => [
                    'order' => 13,
                    'sub_step_template_id' => $template2rows3top1bottom->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('has-solar-panels'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-count'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('total-installed-power'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panels-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                    ]
                ],
                'Warmtepomp interesse' => [
                    'order' => 14,
                    'sub_step_template_id' => $templateDefault->id,
                    // When a user has a full heat pump, we don't ask interest. However, if they don't have a heat
                    // pump at all, we also want to show it. In the case a user changes his heat pump state,
                    // the database could still hold "full heat pump" as answer.
                    'conditions' => [
                        [
                            [
                                'column' => [
                                    'slug->nl' => 'warmtepomp',
                                ],
                                'operator' => Clause::NOT_PASSES,
                                'value' => SubStep::class,
                            ],
                        ],
                        [
                            // Full heat pumps
                            [
                                'column' => 'heat-pump-type',
                                'operator' => Clause::NEQ,
                                'value' => $heatPump->values()->where('calculate_value', 4)->first()->id,
                            ],
                            [
                                'column' => 'heat-pump-type',
                                'operator' => Clause::NEQ,
                                'value' => $heatPump->values()->where('calculate_value', 5)->first()->id,
                            ],
                            [
                                'column' => 'heat-pump-type',
                                'operator' => Clause::NEQ,
                                'value' => $heatPump->values()->where('calculate_value', 6)->first()->id,
                            ],
                        ],
                    ],
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('interested-in-heat-pump'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('interested-in-heat-pump-variant'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'interested-in-heat-pump',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ],
                                    [
                                        'column' => 'fn',
                                        'operator' => 'HasCompletedStep',
                                        'value' => [
                                            'steps' => ['heating'],
                                            'input_source_shorts' => [
                                                InputSource::RESIDENT_SHORT,
                                                InputSource::COACH_SHORT,
                                            ],
                                            'should_pass' => false,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'Samenvatting woningstatus' => [
                    'slug' => 'samenvatting-woonstatus',
                    'order' => 15,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'small-measures' => [
                'Verlichting' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('light'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-led-light'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-lights'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Energiezuinige apparatuur' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('energy-efficient-equipment'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-old-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-unused-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-standby-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-use-full-washing-machine'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-use-full-tumble-dryer'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Energiezuinige installaties' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('energy-efficient-installations'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pump-switch-floor-heating'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-alternating-current-fan'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Kierdichting' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('crack-sealing'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-windows-doors'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('mailbox-bristles'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Verbeteren van de radiatoren' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('improve-radiators'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('radiator-foil'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('no-curtains-for-the-radiator'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-thermostat-knobs'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-radiator-ventilation'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('vent-radiators-frequently'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Verbeteren van de verwarminsinstallatie' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('improve-heating-installations'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('lower-comfort-heat'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulate-hr-boiler-pipes'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hydronic-balancing'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-gas-with-infrared-panels'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Besparen op warm tapwater' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('save-warm-tap-water'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-saving-shower-head'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('shower-shorter'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-boiler'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Algemeen' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('general'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-heat-used-rooms'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-insulating-curtains'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('keep-unheated-rooms-closed'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-door-closers'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Samenvatting kleine maatregelen' => [
                    'order' => 8,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('small-measures-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('small-measures-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
        ]);

        #-------------------------
        # Lite Scan SubSteppables
        #-------------------------
        $this->saveStructure([
            'building-data-lite' => [
                // sub step name
                'Woning type' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-type-category'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Wat voor woning' => [
                    'order' => 1,
                    'conditions' => [
                        [
                            [
                                'column' => 'fn',
                                'operator' => 'BuildingType',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Huur of koop' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-contract-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Bouwjaar en oppervlak' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('build-year'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-layers'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Specifieke voorbeeld woning' => [
                    'order' => 4,
                    'conditions' => [
                        [
                            [
                                'column' => 'fn',
                                'operator' => 'SpecificExampleBuilding',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('specific-example-building'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Monument en energielabel' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('monument'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('energy-label'),
                            'tool_question_type_id' => $radioIconSmallType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Gebruikersoppervlak en bijzonderheden' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('surface'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Samenvatting woninggegevens' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-data-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-data-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'usage-lite-scan' => [
                'Hoeveel bewoners' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('resident-count'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Thermostaat gebruik' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('thermostat-high'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('thermostat-low'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hours-high'),
                            'tool_question_type_id' => $sliderType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Gebruik warm tapwater' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heating-first-floor'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heating-second-floor'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-comfort'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Gas en elektra gebruik' => [
                    'order' => 3,
                    'sub_step_template_id' => $template2rows1top2bottom->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('cook-type'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-gas'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-electricity'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Samenvatting bewoners-gebruik' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('usage-quick-scan-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('usage-quick-scan-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'living-requirements-lite' => [
                'Welke zaken vindt u belangrijk?' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('comfort-priority'),
                            'tool_question_type_id' => $measurePriorityType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Welke grote maatregelen' => [
                    'order' => 1,
                    // note: dit is een custom vraag, zie CustomChanges
                    'sub_step_template_id' => $templateCustomChanges->id,
                ],
                'Samenvatting woonwensen' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'residential-status-lite' => [
                'Verwarming' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-source'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'Verwarming warm water' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'Hoe is de verwarming' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application'),
                            'tool_question_type_id' => $checkboxIconType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                '50 graden test' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::CONTAINS,
                                'value' => 'hr-boiler',
                            ],
                            [
                                [
                                    'column' => 'building-heating-application',
                                    'operator' => Clause::CONTAINS,
                                    'value' => 'radiators',
                                ],
                                [
                                    'column' => 'building-heating-application',
                                    'operator' => Clause::CONTAINS,
                                    'value' => 'air-heating',
                                ],
                            ],
                        ],
                    ],
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('fifty-degree-test'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Ventilatie' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-demand-driven'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 1)->first()->id, // Natuurlijke ventilatie
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-heat-recovery'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 1)->first()->id, // Natuurlijke ventilatie
                                    ],
                                    [
                                        'column' => 'ventilation-type',
                                        'operator' => Clause::NEQ,
                                        'value' => $ventilation->values()->where('calculate_value', 2)->first()->id, // Mechanische ventilatie
                                    ],
                                ],
                            ],
                        ],
                    ]
                ],
                'Kierdichting' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-type'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Samenvatting woningstatus' => [
                    'slug' => 'samenvatting-woonstatus',
                    'order' => 6,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
            'small-measures-lite' => [
                'Verlichting' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('light'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-led-light'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-led-light-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-led-light'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-led-light-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-led-light'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-lights'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Energiezuinige apparatuur' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('energy-efficient-equipment'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-old-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-unused-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-standby-equipment'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-use-full-washing-machine'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-use-full-tumble-dryer'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Energiezuinige installaties' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('energy-efficient-installations'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pump-switch-floor-heating'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pump-switch-floor-heating-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('pump-switch-floor-heating'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pump-switch-floor-heating-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('pump-switch-floor-heating'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-alternating-current-fan'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-alternating-current-fan-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('replace-alternating-current-fan'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-alternating-current-fan-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('replace-alternating-current-fan'),
                        ],
                    ],
                ],
                'Kierdichting' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('crack-sealing'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-windows-doors'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-windows-doors-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('crack-sealing-windows-doors'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-windows-doors-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('crack-sealing-windows-doors'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('mailbox-bristles'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('mailbox-bristles-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('mailbox-bristles'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('mailbox-bristles-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('mailbox-bristles'),
                        ],
                    ],
                ],
                'Verbeteren van de radiatoren' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('improve-radiators'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('radiator-foil'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('radiator-foil-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('radiator-foil'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('radiator-foil-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('radiator-foil'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('no-curtains-for-the-radiator'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-thermostat-knobs'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-thermostat-knobs-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-thermostat-knobs'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-thermostat-knobs-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-thermostat-knobs'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-radiator-ventilation'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-radiator-ventilation-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-radiator-ventilation'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('apply-radiator-ventilation-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('apply-radiator-ventilation'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('vent-radiators-frequently'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('vent-radiators-frequently-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('vent-radiators-frequently'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('vent-radiators-frequently-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('vent-radiators-frequently'),
                        ],
                    ],
                ],
                'Verbeteren van de verwarminsinstallatie' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('improve-heating-installations'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('lower-comfort-heat'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('lower-comfort-heat-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('lower-comfort-heat'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('lower-comfort-heat-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('lower-comfort-heat'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulate-hr-boiler-pipes'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulate-hr-boiler-pipes-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('insulate-hr-boiler-pipes'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulate-hr-boiler-pipes-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('insulate-hr-boiler-pipes'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hydronic-balancing'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hydronic-balancing-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('hydronic-balancing'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hydronic-balancing-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('hydronic-balancing'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-gas-with-infrared-panels'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-gas-with-infrared-panels-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('replace-gas-with-infrared-panels'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('replace-gas-with-infrared-panels-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('replace-gas-with-infrared-panels'),
                        ],
                    ],
                ],
                'Besparen op warm tapwater' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('save-warm-tap-water'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-saving-shower-head'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-saving-shower-head-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('water-saving-shower-head'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-saving-shower-head-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('water-saving-shower-head'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('shower-shorter'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('turn-off-boiler'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Algemeen' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolLabel::findByShort('general'),
                            'tool_question_type_id' => null,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('only-heat-used-rooms'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-insulating-curtains'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('keep-unheated-rooms-closed'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-door-closers'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-door-closers-how'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('use-door-closers'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('use-door-closers-coach-help'),
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-full',
                            'conditions' => $this->getSubQuestionConditions('use-door-closers'),
                        ],
                    ],
                ],
                'Samenvatting kleine maatregelen' => [
                    'order' => 8,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('small-measures-comment-resident'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('small-measures-comment-coach'),
                            'tool_question_type_id' => $textareaPopupType->id,
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
        ]);

        #-------------------------
        # Expert Scan SubSteppables
        #-------------------------
        $newHrBoilerCondition = [
            [
                [
                    'column' => 'new-heat-source',
                    'operator' => Clause::CONTAINS,
                    'value' => 'hr-boiler',
                ],
            ],
            [
                [
                    'column' => 'new-heat-source-warm-tap-water',
                    'operator' => Clause::CONTAINS,
                    'value' => 'hr-boiler',
                ],
            ],
        ];
        $newHeatPumpCondition = [
            [
                [
                    'column' => 'new-heat-source',
                    'operator' => Clause::CONTAINS,
                    'value' => 'heat-pump',
                ],
            ],
            [
                [
                    'column' => 'new-heat-source-warm-tap-water',
                    'operator' => Clause::CONTAINS,
                    'value' => 'heat-pump',
                ],
            ],
        ];
        $newSunBoilerCondition = [
            [
                [
                    [
                        'column' => 'new-heat-source',
                        'operator' => Clause::CONTAINS,
                        'value' => 'sun-boiler',
                    ],
                    [
                        'column' => 'new-heat-source-warm-tap-water',
                        'operator' => Clause::CONTAINS,
                        'value' => 'sun-boiler',
                    ],
                ],
            ],
            [
                [
                    'column' => 'new-heat-source-warm-tap-water',
                    'operator' => Clause::CONTAINS,
                    'value' => 'sun-boiler',
                ],
            ],
        ];

        $this->saveStructure([
            'heating' => [
                'Nieuwe situatie' => [
                    'order' => 0,
                    'morphs' => [
                        // Unconditional questions
                        [
                            'morph' => ToolLabel::findByShort('heat-source'),
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-water-comfort'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-heat-source'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-heat-source-warm-tap-water'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-building-heating-application'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        // HR boiler
                        [
                            'morph' => ToolLabel::findByShort('hr-boiler'),
                            'size' => 'col-span-6',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr-boiler-replace'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        [
                                            'column' => 'new-heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'hr-boiler',
                                        ],
                                        [
                                            'column' => 'new-heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'hr-boiler',
                                        ],
                                    ],
                                    [
                                        [
                                            'column' => 'heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'hr-boiler',
                                        ],
                                        [
                                            'column' => 'heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'hr-boiler',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-boiler-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr-boiler-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr-boiler-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('hr-boiler-cost-indication'),
                            'size' => 'col-span-6',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.amount_gas'),
                            'size' => 'col-span-3',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.replace_year'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('hr-boiler.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => $newHrBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-high-efficiency-boiler-replace-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('hr-boiler', null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-outside-air-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('hr-boiler', null, null, $hrBoilerReplace),
                        ],
                        // Heat pump
                        [
                            'morph' => ToolLabel::findByShort('heat-pump'),
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-replace'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        [
                                            'column' => 'new-heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'heat-pump',
                                        ],
                                        [
                                            'column' => 'new-heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'heat-pump',
                                        ],
                                    ],
                                    [
                                        [
                                            'column' => 'heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'heat-pump',
                                        ],
                                        [
                                            'column' => 'heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'heat-pump',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-boiler-setting-comfort-heat'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-cook-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-heat-pump-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.advised_system.required_power'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-preferred-power'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('outside-unit-space'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('inside-unit-space'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('heat-pump-efficiency-indication'),
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.advised_system.share_heating'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.advised_system.share_tap_water'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.advised_system.scop_heating'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.advised_system.scop_tap_water'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('heat-pump-cost-indication'),
                            'size' => 'col-span-6',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.amount_gas'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.amount_electricity'),
                            'size' => 'col-span-3',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.extra_consumption_electricity'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('heat-pump.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => $newHeatPumpCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-outside-air-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-outside-air'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-outside-air-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-outside-air', $hybridHeatPumpOutsideAir),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-ventilation-air-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-ventilation-air'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-ventilation-air-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-ventilation-air', $hybridHeatPumpVentilationAir),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-pvt-panels-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-pvt-panels'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hybrid-heat-pump-pvt-panels-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'hybrid-heat-pump-pvt-panels', $hybridHeatPumpPvtPanels),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-outside-air-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-outside-air'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-outside-air-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-outside-air', $fullHeatPumpOutsideAir),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-ground-heat-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-ground-heat'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-ground-heat-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-ground-heat', $fullHeatPumpGroundHeat),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-pvt-panels-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-pvt-panels'),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-full-heat-pump-pvt-panels-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('heat-pump', 'new-heat-pump-type', 'full-heat-pump-pvt-panels', $fullHeatPumpPvtPanels),
                        ],
                        // Sun boiler
                        [
                            'morph' => ToolLabel::findByShort('sun-boiler'),
                            'size' => 'col-span-6',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('sun-boiler-replace'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        [
                                            'column' => 'new-heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'sun-boiler',
                                        ],
                                        [
                                            'column' => 'new-heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'sun-boiler',
                                        ],
                                    ],
                                    [
                                        [
                                            'column' => 'heat-source',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'sun-boiler',
                                        ],
                                        [
                                            'column' => 'heat-source-warm-tap-water',
                                            'operator' => Clause::CONTAINS,
                                            'value' => 'sun-boiler',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heater-pv-panel-orientation'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heater-pv-panel-angle'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('sun-boiler-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('sun-boiler-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('sun-boiler-estimate-current-usage'),
                            'size' => 'col-span-6',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.amount_gas'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.consumption.water'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.consumption.gas'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('sun-boiler-specifications'),
                            'size' => 'col-span-6',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.specs.size_boiler'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.specs.size_collector'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolLabel::findByShort('sun-boiler-cost-indication'),
                            'size' => 'col-span-6',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.production_heat'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.percentage_consumption'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.cost_indication'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('sun-boiler.interest_comparable'),
                            'size' => 'col-span-3',
                            'conditions' => $newSunBoilerCondition,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-heater-place-replace-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('sun-boiler', null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-heater-place-replace-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getExpertSubsidyQuestionConditions('sun-boiler', null, null, $heaterPlaceReplace),
                        ],
                        // Heat pump boiler
                        [
                            'morph' => ToolLabel::findByShort('heat-pump-boiler'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'new-heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump-boiler',
                                    ],
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump-boiler',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-boiler-replace'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'new-heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump-boiler',
                                    ],
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump-boiler',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'Huidige situatie' => [
                    'order' => 1,
                    'morphs' => [
                        //[
                        //    'morph' => ToolLabel::findByShort('hr-boiler'),
                        //    'size' => 'col-span-6',
                        //],
                        [
                            'morph' => ToolQuestion::findByShort('surface'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('resident-count'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('cook-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-gas'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-electricity'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-comfort'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'heat-source-warm-tap-water',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-type'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
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
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
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
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application'),
                            'tool_question_type_id' => $multiDropdownType->id,
                            'size' => 'col-span-6',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application-other'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'none',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('fifty-degree-test'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'radiators',
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'heat-source',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'air-heating',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-setting-comfort-heat'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-wall-insulation'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-floor-insulation'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-roof-insulation'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-living-rooms-windows'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-sleeping-rooms-windows'),
                            'tool_question_type_id' => $dropdownType->id,
                            'size' => 'col-span-3',
                        ],
                    ],
                ],
            ],
            // TODO: Legacy tool labels
            'ventilation' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-how'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $multiDropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-living-situation'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $multiDropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-usage'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $multiDropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-balanced-wtw-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-decentral-wtw-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-demand-driven-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('ventilation.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('ventilation.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('ventilation.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('ventilation.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('ventilation.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-crack-sealing-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'crack-sealing-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-crack-sealing-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'crack-sealing-considerable', 1, $crackSealingMeasureApplication),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-balanced-wtw-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-balanced-wtw-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-balanced-wtw-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-balanced-wtw-considerable', 1, $ventilationBalancedWtw),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-decentral-wtw-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-decentral-wtw-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-decentral-wtw-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-decentral-wtw-considerable', 1, $ventilationDecentralWtw),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-demand-driven-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-demand-driven-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-ventilation-demand-driven-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'ventilation-demand-driven-considerable', 1, $ventilationDemandDriven),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
            'wall-insulation' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('wall-insulation-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-wall-insulation'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('has-cavity-wall'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wall-facade-plastered-painted'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wall-facade-plastered-painted-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('damaged-paintwork'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wall-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulation-wall-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('damaged-wall-joints'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('contaminated-wall-joints'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.savings_gas'),
                            'size' => 'col-span-2',
                             'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.savings_co2'),
                            'size' => 'col-span-2',
                             'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.savings_money'),
                            'size' => 'col-span-2',
                             'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.cost_indication'),
                            'size' => 'col-span-2',
                             'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.interest_comparable'),
                            'size' => 'col-span-2',
                             'conditions' => [
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-wall-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $wallInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-cavity-wall-insulation-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 2,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 0,
                                    ],
                                ],
                            ], null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-cavity-wall-insulation-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 2,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 0,
                                    ],
                                ],
                            ], null, null, $cavityWallInsulation),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-facade-wall-insulation-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ], 'has-cavity-wall', 2),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-facade-wall-insulation-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ], 'has-cavity-wall', 2, $facadeWallInsulation),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-wall-insulation-research-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ], 'has-cavity-wall', 0),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-wall-insulation-research-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'wall-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ], 'has-cavity-wall', 0, $wallInsulationResearch),
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.repair_joint.costs'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.repair_joint.year'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.clean_brickwork.costs'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.clean_brickwork.year'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.impregnate_wall.costs'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.impregnate_wall.year'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.paint_wall.costs'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('wall-insulation.paint_wall.year'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-cavity-wall-insulation-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 2,
                                    ],
                                    [
                                        'column' => 'has-cavity-wall',
                                        'operator' => Clause::NEQ,
                                        'value' => 0,
                                    ],
                                ],
                            ], null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wall-insulation-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wall-insulation-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
            'insulated-glazing' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-only-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-only-current-glass'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-only-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-only-rooms-heated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-only-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-only-replacement-glass-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-only-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-only-replacement-glass-count'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-only-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hrpp-glass-only-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hrpp-glass-only-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hrpp-glass-only-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hrpp-glass-only-considerable', 1, $hrppGlassOnly),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-frame-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-frame-current-glass'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-frame-rooms-heated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-frame-replacement-glass-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hrpp-glass-frame-replacement-glass-count'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hrpp-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hrpp-glass-frames-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hrpp-glass-frame-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hrpp-glass-frames-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hrpp-glass-frame-considerable', 1, $hrppGlassFrames),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr3p-glass-frame-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr3p-glass-frame-current-glass'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hr3p-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr3p-glass-frame-rooms-heated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hr3p-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr3p-glass-frame-replacement-glass-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hr3p-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hr3p-glass-frame-replacement-glass-count'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'hr3p-glass-frame-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hr3p-frames-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hr3p-glass-frame-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-hr3p-frames-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'hr3p-glass-frame-considerable', 1, $hr3pFrames),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('glass-in-lead-replace-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('glass-in-lead-replace-current-glass'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'glass-in-lead-replace-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('glass-in-lead-replace-rooms-heated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'glass-in-lead-replace-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('glass-in-lead-replace-glass-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'glass-in-lead-replace-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('glass-in-lead-replace-glass-count'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'glass-in-lead-replace-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-glass-in-lead-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'glass-in-lead-replace-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-glass-in-lead-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'glass-in-lead-replace-considerable', 1, $glassInLead),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('total-window-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('frame-type'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],

                        [
                            'morph' => ToolQuestion::findByShort('wood-elements'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $multiDropdownType->id,
                        ],

                        [
                            'morph' => ToolQuestion::findByShort('last-painted-year'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $textType->id,
                        ],

                        [
                            'morph' => ToolQuestion::findByShort('paintwork-status'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('wood-rot-status'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.paintwork.costs'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('insulated-glazing.paintwork.year'),
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulated-glazing-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulated-glazing-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
            'floor-insulation' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('floor-insulation-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-floor-insulation'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('has-crawlspace'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crawlspace-accessible'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('crawlspace-height'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('floor-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('insulation-floor-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('floor-insulation.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('floor-insulation.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('floor-insulation.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('floor-insulation.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('floor-insulation.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-floor-insulation',
                                        'operator' => Clause::EQ,
                                        'value' => $floorInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-floor-insulation-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::EQ,
                                        'value' => $crawlSpaceHigh->id,
                                    ],
                                ],
                            ], null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-floor-insulation-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::EQ,
                                        'value' => $crawlSpaceHigh->id,
                                    ],
                                ],
                            ], null, null, $floorInsulation),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-bottom-insulation-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::EQ,
                                        'value' => $crawlSpaceMid->id,
                                    ],
                                ],
                            ], null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-bottom-insulation-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::EQ,
                                        'value' => $crawlSpaceMid->id,
                                    ],
                                ],
                            ], null, null, $bottomInsulation),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-floor-insulation-research-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::NEQ,
                                        'value' => $crawlSpaceHigh->id,
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::NEQ,
                                        'value' => $crawlSpaceMid->id,
                                    ],
                                ],
                            ], null, null),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-floor-insulation-research-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'floor-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'has-crawlspace',
                                        'operator' => Clause::NEQ,
                                        'value' => 'no',
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::NEQ,
                                        'value' => $crawlSpaceHigh->id,
                                    ],
                                    [
                                        'column' => 'crawlspace-height',
                                        'operator' => Clause::NEQ,
                                        'value' => $crawlSpaceMid->id,
                                    ],
                                ],
                            ], null, null, $floorInsulationResearch),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('floor-insulation-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('floor-insulation-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
            'roof-insulation' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('roof-insulation-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-roof-types'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $multiDropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('is-pitched-roof-insulated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-insulation-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-zinc-replaced-date'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-tiles-condition'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-insulation'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('pitched-roof-heating'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.replace.costs'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.pitched.replace.year'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-pitched-inside-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ], 'pitched-roof-insulation', $roofInsulationPitchedInside->id),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-pitched-inside-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ], 'pitched-roof-insulation', $roofInsulationPitchedInside->id, $roofInsulationPitchedInside),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-pitched-replace-tiles-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ], 'pitched-roof-insulation', $roofInsulationPitchedReplaceTiles->id),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-pitched-replace-tiles-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $pitchedRoof->id,
                                    ],
                                ],
                            ], 'pitched-roof-insulation', $roofInsulationPitchedReplaceTiles->id, $roofInsulationPitchedReplaceTiles),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('is-flat-roof-insulated'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-insulation-surface'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-zinc-replaced-date'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-bitumen-replaced-date'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $textType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-insulation'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('flat-roof-heating'),
                            'size' => 'col-span-6',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.savings_gas'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationNone->id,
                                    ],
                                ],
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                    [
                                        'column' => 'is-pitched-roof-insulated',
                                        'operator' => Clause::EQ,
                                        'value' => $roofInsulationUnknown->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.replace.costs'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('roof-insulation.flat.replace.year'),
                            'size' => 'col-span-2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-flat-current-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ], 'flat-roof-insulation', $roofInsulationFlatCurrent->id),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-flat-current-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ], 'flat-roof-insulation', $roofInsulationFlatCurrent->id, $roofInsulationFlatCurrent),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-flat-replace-current-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ], 'flat-roof-insulation', $roofInsulationFlatReplaceCurrent->id),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-roof-insulation-flat-replace-current-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([
                                [
                                    [
                                        'column' => 'roof-insulation-considerable',
                                        'operator' => Clause::EQ,
                                        'value' => 1,
                                    ],
                                    [
                                        'column' => 'current-roof-types',
                                        'operator' => Clause::CONTAINS,
                                        'value' => $flatRoof->id,
                                    ],
                                ],
                            ], 'flat-roof-insulation', $roofInsulationFlatReplaceCurrent->id, $roofInsulationFlatReplaceCurrent),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('roof-insulation-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('roof-insulation-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
            'solar-panels' => [
                'Hoofd vragen' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('solar-panels-considerable'),
                            'size' => 'col-span-6',
                            'conditions' => [],
                            'tool_question_type_id' => $dropdownType->id,
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-electricity'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-peak-power'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('has-solar-panels'),
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'col-span-3',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-count'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('total-installed-power'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panels-placed-date'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'has-solar-panels',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ]
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('desired-solar-panel-count'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-orientation'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-angle'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.yield_electricity'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.raise_own_consumption'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.savings_co2'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.savings_money'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.cost_indication'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolCalculationResult::findByShort('solar-panels.interest_comparable'),
                            'size' => 'col-span-2',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-solar-panels-place-replace-own-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'solar-panels-considerable', 1),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('user-costs-solar-panels-place-replace-subsidy-total'),
                            'tool_question_type_id' => $textType->id,
                            'size' => 'col-span-2',
                            'conditions' => $this->getSubsidyQuestionConditions([], 'solar-panels-considerable', 1, $solarPanelsPlaceReplace),
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panels-comment-resident'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panels-comment-coach'),
                            'tool_question_type_id' => $textareaType->id,
                            'size' => 'col-span-3',
                            'conditions' => [],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function saveStructure($structure)
    {
        foreach ($structure as $stepShort => $subQuestions) {
            $step = Step::findByShort($stepShort);

            foreach ($subQuestions as $subQuestionName => $subQuestionData) {

                $subStepSlug = $subQuestionData['slug'] ?? Str::slug($subQuestionName);
                $names = ['nl' => $subQuestionName];
                $slugs = ['nl' => $subStepSlug];

                $subStepData = [
                    'name' => json_encode($names),
                    'order' => $subQuestionData['order'],
                    'slug' => json_encode($slugs),
                    'step_id' => $step->id,
                    'sub_step_template_id' => $subQuestionData['sub_step_template_id'] ?? null,
                ];

                if (isset($subQuestionData['conditions'])) {
                    $subStepData['conditions'] = json_encode($subQuestionData['conditions']);
                }

                $subStep = SubStep::where('slug->nl', $slugs['nl'])
                    ->where('step_id', $subStepData['step_id'])
                    ->first();

                // Usually we do an updateOrInsert, but since we have to use a JSON column to compare, we can't use
                // it. The query builder won't properly handle unencoded JSON, but we need unencoded JSON to
                // compare.
                if ($subStep instanceof SubStep) {
                    DB::table('sub_steps')->where('id', $subStep->id)->update($subStepData);
                } else {
                    DB::table('sub_steps')->insert($subStepData);

                    // Fetch again
                    $subStep = SubStep::where('slug->nl', $slugs['nl'])
                        ->where('step_id', $subStepData['step_id'])
                        ->first();
                }

                if (isset($subQuestionData['morphs'])) {
                    $orderForSubStepToolQuestions = 0;
                    foreach ($subQuestionData['morphs'] as $index => $morph) {
                        $conditions = $morph['conditions'] ?? null;

                        DB::table('sub_steppables')->updateOrInsert(
                            [
                                'sub_step_id' => $subStep->id,
                                'sub_steppable_id' => $morph['morph']->id,
                                'sub_steppable_type' => $morph['morph']->getMorphClass(),
                            ],
                            [
                                'order' => $orderForSubStepToolQuestions,
                                'tool_question_type_id' => $morph['tool_question_type_id'] ?? null,
                                'conditions' => is_null($conditions) ? null : json_encode($conditions),
                                'size' => $morph['size'] ?? null,
                            ],
                        );

                        $orderForSubStepToolQuestions++;
                    }
                }
            }
        }
    }

    private function getSubQuestionConditions(string $short): array
    {
        return [
            [
                [
                    'column' => $short,
                    'operator' => Clause::EQ,
                    'value' => 'want-to',
                ],
            ],
        ];
    }

    private function getExpertSubsidyQuestionConditions(string $newShort, ?string $question, ?string $value, $advisable = null): array
    {
        $conditions = [
            [
                [
                    [
                        'column' => 'new-heat-source',
                        'operator' => Clause::CONTAINS,
                        'value' => $newShort,
                    ],
                    [
                        'column' => 'new-heat-source-warm-tap-water',
                        'operator' => Clause::CONTAINS,
                        'value' => $newShort,
                    ],
                ],
            ],
        ];
        return $this->getSubsidyQuestionConditions($conditions, $question, $value, $advisable);
    }

    private function getSubsidyQuestionConditions(array $conditions, ?string $question, ?string $value, $advisable = null): array
    {
        if (! is_null($question)) {
            $conditions[0][] = [
                'column' => $question,
                'operator' => Clause::EQ,
                'value' => $value,
            ];
        }

        if (! is_null($advisable)) {
            $conditions[0][] = [
                'column' => 'fn',
                'operator' => 'MeasureHasSubsidy',
                'value' => [
                    'advisable_type' => get_class($advisable),
                    'advisable_id' => $advisable->id,
                ],
            ];
        }

        return $conditions;
    }
}