<?php

use App\Helpers\Conditions\Clause;
use App\Models\Service;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\ToolLabel;
use App\Models\ToolQuestion;
use App\Models\SubSteppable;
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
        $templateSpecificExampleBuilding = SubStepTemplate::findByShort('specific-example-building');

        // Some services for conditions
        $heatPump = Service::findByShort('heat-pump');
        $ventilation = Service::findByShort('house-ventilation');

        #-------------------------
        # Quick Scan sub steppables
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
                                'value' => 'BuildingType',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-type'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Wat voor dak' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('roof-type'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Bouwjaar en oppervlak' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('build-year'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-layers'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Specifieke voorbeeld woning' => [
                    'order' => 3,
                    'conditions' => [
                        [
                            [
                                'column' => 'fn',
                                'value' => 'SpecificExampleBuilding',
                            ],
                        ],
                    ],
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('specific-example-building'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('energy-label'),
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
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-data-comment-coach'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('thermostat-low'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('hours-high'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heating-second-floor'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-comfort'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-gas'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('amount-electricity'),
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
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('usage-quick-scan-comment-coach'),
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
                            'size' => 'w-full',
                        ],
                    ],
                ],
                'Welke zaken vervangen' => [
                    'order' => 2,
                    // note: dit is een custom vraag, zie slide 18
                    'sub_step_template_id' => $templateCustomChanges->id,
                ],
                'Samenvatting woonwensen' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-resident'),
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('living-requirements-comment-coach'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-other'),
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water-other'),
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
                'Zonnenboiler' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heater-type'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Gasketel vragen' => [
                    'order' => 8,
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-placed-date'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Warmtepomp' => [
                    'order' => 9,
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::CONTAINS,
                                'value' => 'heat-pump',
                            ],
                        ],
                        [
                            [
                                'column' => 'heat-source-warm-tap-water',
                                'operator' => Clause::CONTAINS,
                                'value' => 'heat-pump',
                            ],
                        ],
                    ],
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-type'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-placed-date'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Hoe is de verwarming' => [
                    'order' => 10,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application-other'),
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
                    'order' => 11,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('fifty-degree-test'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'building-heating-application',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'radiators',
                                    ],
                                ],
                                [
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
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Ventilatie' => [
                    'order' => 12,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-type'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('ventilation-demand-driven'),
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
                    'order' => 13,
                    'sub_step_template_id' => $templateDefault->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('crack-sealing-type'),
                            'size' => 'w-full',
                        ],
                    ]
                ],
                'Zonnepanelen' => [
                    'order' => 14,
                    'sub_step_template_id' => $template2rows3top1bottom->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('has-solar-panels'),
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('solar-panel-count'),
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
                    'order' => 15,
                    'sub_step_template_id' => $templateDefault->id,
                    // When a user has a full heat pump, we don't ask interest. However, if they don't have a heat
                    // pump at all, we also want to show it. In the case a user changes his heat pump state,
                    // the database could still hold "full heat pump" as answer.
                    'conditions' => [
                        [
                            // No heat pump selected
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::NOT_CONTAINS,
                                'value' => 'heat-pump',
                            ],
                            [
                                'column' => 'heat-source-warm-tap-water',
                                'operator' => Clause::NOT_CONTAINS,
                                'value' => 'heat-pump',
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
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('interested-in-heat-pump-variant'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'interested-in-heat-pump',
                                        'operator' => Clause::EQ,
                                        'value' => 'yes',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'Samenvatting woningstatus' => [
                    'slug' => 'samenvatting-woonstatus',
                    'order' => 16,
                    'sub_step_template_id' => $templateSummary->id,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-resident'),
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-element-comment-coach'),
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-resident'),
                            'size' => 'w-1/2',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('residential-status-service-comment-coach'),
                            'size' => 'w-1/2',
                        ],
                    ],
                ],
            ],
        ]);

        #-------------------------
        # Expert sub steppables
        #-------------------------
        $this->saveStructure([
            'heating' => [
                'nieuwe situatie' => [
                    'order' => 0,
                    'morphs' => [
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-considerable'),
                            'size' => 'w-full',
                            // TODO: CheckboxIconType
                        ],
                        [
                            'morph' => ToolLabel::findByShort('hr-boiler'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-boiler-type'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'hr-boiler',
                                    ],
                                ],
                            ],
                            // TODO: Dropdown
                        ],
                        // TODO: Calculate fields
                        [
                            'morph' => ToolLabel::findByShort('heat-pump'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],

                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-building-heating-application'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: CheckboxIconType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-boiler-setting-comfort-heat'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: RadioIconType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-cook-type'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: RadioIconType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('new-heat-pump-type'),
                            'size' => 'w-full',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: Dropdown
                        ],
                        // TODO: Indication required power?
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-preferred-power'),
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: Text Input
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('outside-unit-space'),
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('inside-unit-space'),
                            'size' => 'w-1/2',
                            'conditions' => [
                                [
                                    [
                                        'column' => 'heat-source-considerable',
                                        'operator' => Clause::CONTAINS,
                                        'value' => 'heat-pump',
                                    ],
                                ],
                            ],
                            // TODO: Dropdown
                        ],
                    ],
                ],
                'huidige situatie' => [
                    'order' => 1,
                    'morphs' => [
                        //[
                        //    'morph' => ToolLabel::findByShort('hr-boiler'),
                        //    'size' => 'w-full',
                        //],
                        [
                            'morph' => ToolQuestion::findByShort('surface'),
                            'size' => 'w-1/2',
                            // TODO: Text input
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('resident-count'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('cook-type'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [   // TODO: Double questions? (See sun-boiler)
                            'morph' => ToolQuestion::findByShort('water-comfort'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source'),
                            'size' => 'w-full',
                            // TODO: DropdownMultiType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-source-warm-tap-water'),
                            'size' => 'w-full',
                            // TODO: DropdownMultiType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-type'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-placed-date'),
                            'size' => 'w-1/2',
                            // TODO: Text Input
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-type'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        // TODO: Not in mockup, missing field for "other" option of heat-pump-type????
                        [
                            'morph' => ToolQuestion::findByShort('heat-pump-placed-date'),
                            'size' => 'w-1/2',
                            // TODO: Text Input
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('building-heating-application'),
                            'size' => 'w-full',
                            // TODO: DropdownMultiType
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('fifty-degree-test'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('boiler-setting-comfort-heat'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-wall-insulation'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-floor-insulation'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-roof-insulation'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-living-rooms-windows'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('current-sleeping-rooms-windows'),
                            'size' => 'w-1/2',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolLabel::findByShort('sun-boiler'),
                            'size' => 'w-full',
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('water-comfort'),
                            'size' => 'w-1/3',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heater-pv-panel-orientation'),
                            'size' => 'w-1/3',
                            // TODO: Dropdown
                        ],
                        [
                            'morph' => ToolQuestion::findByShort('heater-pv-panel-angle'),
                            'size' => 'w-1/3',
                            // TODO: Dropdown
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
                    foreach ($subQuestionData['morphs'] as $morph) {
                        $conditions = $morph['conditions'] ?? null;

                        DB::table('sub_steppables')->updateOrInsert(
                            [
                                'sub_step_id' => $subStep->id,
                                'sub_steppable_id' => $morph['morph']->id,
                                'sub_steppable_type' => $morph['morph']->getMorphClass(),
                            ],
                            [
                                'order' => $orderForSubStepToolQuestions,
                                'size' => $morph['size'] ?? null,
                                'conditions' => is_null($conditions) ? null : json_encode($conditions)
                            ],
                        );

                        $orderForSubStepToolQuestions++;
                    }
                }
            }
        }
    }
}