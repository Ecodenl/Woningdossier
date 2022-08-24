<?php

use App\Helpers\Conditions\Clause;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use Illuminate\Database\Seeder;
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

        $this->saveStructure([
            'building-data' => [
                // sub step name
                'Woning type' => [
                    'order' => 0,
                    // question data
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'building-type-category' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
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
                        'building-type' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ]
                ],
                'Wat voor dak' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'roof-type' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ]
                ],
                'Bouwjaar en oppervlak' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'build-year' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                        'building-layers' => [
                            'tool_question_type_id' => $sliderType->id,
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
                    'questions' => [
                        'specific-example-building' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ],
                ],
                'Monument en energielabel' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'monument' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'energy-label' => [
                            'tool_question_type_id' => $radioIconSmallType->id,
                        ],
                    ]
                ],
                'Gebruikersoppervlak en bijzonderheden' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'surface' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                    ],
                ],
                'Samenvatting woninggegevens' => [
                    'order' => 7,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [
                        'building-data-comment-resident' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                        'building-data-comment-coach' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                    ],
                ],
            ],
            'usage-quick-scan' => [
                'Hoeveel bewoners' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'resident-count' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ]
                ],
                'Thermostaat gebruik' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'thermostat-high' => [
                            'tool_question_type_id' => $sliderType->id,
                        ],
                        'thermostat-low' => [
                            'tool_question_type_id' => $sliderType->id,
                        ],
                        'hours-high' => [
                            'tool_question_type_id' => $sliderType->id,
                        ],
                    ],
                ],
                'Gebruik warm tapwater' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heating-first-floor' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'heating-second-floor' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'water-comfort' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ]
                ],
                'Gas en elektra gebruik' => [
                    'order' => 3,
                    'sub_step_template_id' => $template2rows1top2bottom->id,
                    'questions' => [
                        'cook-type' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                        'amount-gas' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                        'amount-electricity' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                    ]
                ],
                'Samenvatting bewoners-gebruik' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [
                        'usage-quick-scan-comment-resident' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                        'usage-quick-scan-comment-coach' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                    ],
                ],
            ],
            'living-requirements' => [
                'Hoelang blijven wonen' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'remaining-living-years' => [
                            'tool_question_type_id' => $sliderType->id,
                        ],
                    ],
                ],
                'Welke zaken vindt u belangrijk?' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'comfort-priority' => [
                            'tool_question_type_id' => $measurePriorityType->id,
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
                    'questions' => [
                        'living-requirements-comment-resident' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                        'living-requirements-comment-coach' => [
                            'tool_question_type_id' => $textareaPopupType->id
                        ],
                    ],
                ],
            ],
            'residential-status' => [
                'Muurisolatie' => [
                    'order' => 0,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-wall-insulation' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ]
                ],
                'Vloerisolatie' => [
                    'order' => 1,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-floor-insulation' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ],
                ],
                'Dakisolatie' => [
                    'order' => 2,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-roof-insulation' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ],
                ],
                'Glasisolatie eerste woonlaag' => [
                    'order' => 3,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-living-rooms-windows' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ]
                ],
                'Glasisolatie tweede woonlaag' => [
                    'order' => 4,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'current-sleeping-rooms-windows' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                    ],
                ],
                'Verwarming' => [
                    'order' => 5,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heat-source' => [
                            'tool_question_type_id' => $checkboxIconType->id,
                        ],
                        'heat-source-warm-tap-water' => [
                            'tool_question_type_id' => $checkboxIconType->id,
                        ],
                    ],
                ],
                'Zonnenboiler' => [
                    'order' => 6,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'heater-type' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
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
                        'boiler-type' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'boiler-placed-date' => [
                            'tool_question_type_id' => $textType->id,
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
                    'questions' => [
                        'heat-pump-type' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'heat-pump-placed-date' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                    ]
                ],
                'Hoe is de verwarming' => [
                    'order' => 9,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'building-heating-application' => [
                            'tool_question_type_id' => $checkboxIconType->id,
                        ],
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
                        'fifty-degree-test' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ]
                ],
                'Ventilatie' => [
                    'order' => 11,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'ventilation-type' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'ventilation-demand-driven' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                        'ventilation-heat-recovery' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ]
                ],
                'Kierdichting' => [
                    'order' => 12,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'crack-sealing-type' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ]
                ],
                'Zonnepanelen' => [
                    'order' => 13,
                    'sub_step_template_id' => $template2rows3top1bottom->id,
                    'questions' => [
                        'has-solar-panels' => [
                            'tool_question_type_id' => $radioIconType->id,
                        ],
                        'solar-panel-count' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                        'total-installed-power' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                        'solar-panels-placed-date' => [
                            'tool_question_type_id' => $textType->id,
                        ],
                    ]
                ],
                'Warmtepomp interesse' => [
                    'order' => 14,
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        'interested-in-heat-pump' => [
                            'tool_question_type_id' => $radioType->id,
                        ],
                    ],
                ],
                'Samenvatting woningstatus' => [
                    'slug' => 'samenvatting-woonstatus',
                    'order' => 15,
                    'sub_step_template_id' => $templateSummary->id,
                    'questions' => [
                        'residential-status-element-comment-resident' => [
                            'tool_question_type_id' => $textareaPopupType->id,
                        ],
                        'residential-status-element-comment-coach' => [
                            'tool_question_type_id' => $textareaPopupType->id,
                        ],
                        'residential-status-service-comment-resident' => [
                            'tool_question_type_id' => $textareaPopupType->id,
                        ],
                        'residential-status-service-comment-coach' => [
                            'tool_question_type_id' => $textareaPopupType->id,
                        ],
                    ],
                ],
            ],
        ]);

        // TODO: Remove when testing finished
        $this->saveStructure([
            'heating' => [
                'huidige situatie' => [
                    'order' => 0,
                    'questions' => [
                        'resident-count' => [
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-1/2'
                        ],
                        'water-comfort' => [
                            'tool_question_type_id' => $radioType->id,
                            'size' => 'w-1/2'
                        ],
                    ],
                ],
                'nieuwe situatie' => [
                    'order' => 0,
                    'questions' => [
                        'amount-electricity' => [
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2'
                        ],
                    ],
                ],
                'Zonnepanelen' => [
                    'order' => 13,
                    'sub_step_template_id' => $template2rows3top1bottom->id,
                    'questions' => [
                        'has-solar-panels' => [
                            'tool_question_type_id' => $radioIconType->id,
                            'size' => 'w-1/2'
                        ],
                        'solar-panel-count' => [
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2'
                        ],
                        'total-installed-power' => [
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2'
                        ],
                        'solar-panels-placed-date' => [
                            'tool_question_type_id' => $textType->id,
                            'size' => 'w-1/2'
                        ],
                    ]
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