<?php

use App\Helpers\Conditions\Clause;
use App\Models\BuildingHeating;
use App\Models\BuildingType;
use App\Models\BuildingTypeCategory;
use App\Models\ComfortLevelTapWater;
use App\Models\Element;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubStepsTableSeeder extends Seeder
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


        $this->saveStructure([
            'building-data' => [
                // sub step name
                'Woning type' => [
                    'order' => 0,
                    // question data
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'building-type-category' => [],
                    ]
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
                    // question data
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'building-type' => [],
                    ]
                ],
                'Wat voor dak' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'roof-type' => [],
                    ]
                ],
                'Bouwjaar en oppervlak' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'build-year' => [],
                        'building-layers' => [],
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
                    'questions' => [
                        'specific-example-building' => [],
                    ],
                ],
                'Monument en energielabel' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'monument' => [],
                        'energy-label' => [],
                    ]
                ],
                'Gebruikersoppervlak en bijzonderheden' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'surface' => [],
                    ],
                ],
                'Samenvatting woninggegevens' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [
                        'building-data-comment-resident' => [],
                        'building-data-comment-coach' => [],
                    ],
                ],
            ],
            'usage-quick-scan' => [
                'Hoeveel bewoners' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'resident-count' => [],
                    ]
                ],
                'Thermostaat gebruik' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'thermostat-high' => [],
                        'thermostat-low' => [],
                        'hours-high' => [],
                    ],
                ],
                'Gebruik warm tapwater' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heating-first-floor' => [],
                        'heating-second-floor' => [],
                        'water-comfort' => [],
                    ]
                ],
                'Gas en elektra gebruik' => [
                    'order' => 3,
                    'sub_step_template_id' => $template2rows1top2bottom->id,
                    'questions' => [
                        'cook-type' => [],
                        'amount-gas' => [],
                        'amount-electricity' => [],
                    ]
                ],
                'Samenvatting bewoners-gebruik' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [
                        'usage-quick-scan-comment-resident' => [],
                        'usage-quick-scan-comment-coach' => [],
                    ],
                ],
            ],
            'living-requirements' => [
                'Hoelang blijven wonen' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'remaining-living-years' => [],
                    ],
                ],
                'Welke zaken vindt u belangrijk?' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'comfort-priority' => [],
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
                    'questions' => [
                        'living-requirements-comment-resident' => [],
                        'living-requirements-comment-coach' => [],
                    ],
                ],
            ],
            'residential-status' => [
                'Muurisolatie' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-wall-insulation' => [],
                    ]
                ],
                'Vloerisolatie' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-floor-insulation' => [],
                    ],
                ],
                'Dakisolatie' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-roof-insulation' => [],
                    ],
                ],
                'Glasisolatie eerste woonlaag' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-living-rooms-windows' => [],
                    ]
                ],
                'Glasisolatie tweede woonlaag' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-sleeping-rooms-windows' => [],
                    ],
                ],
                'Verwarming' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heat-source' => [],
                        'heat-source-warm-tap-water' => [],
                    ],
                ],
                'Zonnenboiler' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heater-type' => [],
                    ]
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
                    ],
                    'questions' => [
                        'boiler-type' => [],
                        'boiler-placed-date' => [],
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
                    'questions' => [
                        'heat-pump-type' => [],
                        'heat-pump-placed-date' => [],
                    ]
                ],

                'Hoe is de verwarming' => [
                    'order' => 9,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'building-heating-application' => [],
                    ]
                ],
                '50 graden test' => [
                    'order' => 10,
                    'sub_step_template_id' => $templateDefault->id,
                    'conditions' => [
                        [
                            [
                                'column' => 'heat-source',
                                'operator' => Clause::CONTAINS,
                                'value' => 'hr-boiler',
                            ],
                        ],
                    ],
                    'questions' => [
                        'fifty-degree-test' => [],
                    ]
                ],

                'Ventilatie' => [
                    'order' => 11,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'ventilation-type' => [],
                        'ventilation-demand-driven' => [],
                        'ventilation-heat-recovery' => [],
                    ]
                ],
                'Kierdichting' => [
                    'order' => 12,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'crack-sealing-type' => [],
                    ]
                ],

                'Zonnepanelen' => [
                    'order' => 13,
                    'sub_step_template_id' => $template2rows3top1bottom->id,
                    'questions' => [
                        'has-solar-panels' => [],
                        'solar-panel-count' => [],
                        'total-installed-power' => [],
                        'solar-panels-placed-date' => [],
                    ]
                ],

                'Warmtepomp interesse' => [
                    'order' => 14,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'interested-in-heat-pump' => [],
                    ],
                ],
                'Samenvatting woningstatus' => [
                    'slug' => 'samenvatting-woonstatus',
                    'order' => 15,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [

                        'residential-status-element-comment-resident' => [],

                        'residential-status-element-comment-coach' => [],

                        'residential-status-service-comment-resident' => [],

                        'residential-status-service-comment-coach' => [],

                    ],
                ],
            ],
        ]);

        $this->saveStructure([
            'heating' => [
                'huidige situatie' => [
                    'order' => 0,
                    'questions' => [
                        'resident-count' => ['size' => 'w-1/2'],
                        'water-comfort' => ['size' => 'w-1/2'],
                    ],
                ]
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

                if (isset($subQuestionData['questions'])) {
                    $orderForSubStepToolQuestions = 0;
                    // the $extra will be a array, it COULD hold anything. For now it will be empty or contain a size.
                    foreach ($subQuestionData['questions'] as $toolQuestionShort => $extra) {

                        $toolQuestion = ToolQuestion::where('short', $toolQuestionShort)
                            ->first();

                        // It might be attached, it might not. We detach to be safe.
                        $subStep->toolQuestions()->detach($toolQuestion);

                        $attributes = array_merge(['order' => $orderForSubStepToolQuestions], $extra);
                        $subStep->toolQuestions()->attach($toolQuestion, $attributes);

                        $orderForSubStepToolQuestions++;
                    }
                }
            }
        }
    }
}