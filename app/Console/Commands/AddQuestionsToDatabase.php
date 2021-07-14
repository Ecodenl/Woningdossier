<?php

namespace App\Console\Commands;

use App\Models\BuildingHeating;
use App\Models\BuildingType;
use App\Models\EnergyLabel;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\SubStepToolQuestion;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\ToolQuestionValueable;
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

        ToolQuestionValueable::truncate();

        $buildingTypes = BuildingType::all();
        $radioIconType = ToolQuestionType::findByShort('radio-icon');
        $radioType = ToolQuestionType::findByShort('radio');
        $textType = ToolQuestionType::findByShort('text');
        $sliderType = ToolQuestionType::findByShort('slider');
        $textareaType = ToolQuestionType::findByShort('textarea');

        $templateDefault = SubStepTemplate::findByShort('template-default');
        $template2rows1top2bottom = SubStepTemplate::findByShort('template-2-rows-1-top-2-bottom');

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
                // wat voor type aappartament heeft u moet nog komen.
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
                // wat voor type aappartament heeft u moet nog komen.
                'Monument en energielabel' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['numeric', 'between:1900,' . date('Y')],
                            'save_in' => 'building_features.monument',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.monument',
                            'tool_question_type_id' => $radioType->id,
                            // todo:work this out
//                            'tool_question_values' => [
//                                1 => __('woningdossier.cooperation.radiobutton.yes'),
//                                2 => __('woningdossier.cooperation.radiobutton.no'),
//                                0 => __('woningdossier.cooperation.radiobutton.unknown'),
//                            ],
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
                // wat voor type aappartament heeft u moet nog komen.
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
                            'validation' => ['numeric', 'min:20', 'max:999999'],
                            // todo: find the right column to save this at
                            'save_in' => 'building_features.surface',
                            'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface',
                            'tool_question_type_id' => $textareaType->id,
                        ],
                    ]
                ],
            ],
            'usage' => [
                'Hoeveel bewoners' => [
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'exists:residents,id'],
                            'save_in' => 'user_energy_habits.resident_count',
                            'translation' => 'cooperation/tool/general-data/usage.index.water-gas.resident-count',
                            'tool_question_type_id' => $radioIconType->id,
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
                            'unit_of_measure' =>__('general.unit.degrees.title'),
                        ],
                        [
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'save_in' => 'user_energy_habits.thermostat_low',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-low',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 10, 'max' => 30, 'value' => 12, 'step' => 1],
                            'unit_of_measure' =>__('general.unit.degrees.title'),
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
                        // hoe wordt er gekookt moet nog toegevoegd worden, dit is alleen een nieuwe vraag \-_-/
                        // die staat op slide 15
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
                    // todo new question
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'validation' => ['required', 'numeric', 'min:10', 'max:30'],
                            'save_in' => 'user_energy_habits.thermostat_high',
                            'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-high',
                            'tool_question_type_id' => $sliderType->id,
                            'options' => ['min' => 1, 'max' => 10, 'value' => 5, 'step' => 1],
                            'unit_of_measure' =>__('general.unit.degrees.title'),
                        ],

                    ]
                ],
            ],
        ];
        foreach ($structure as $stepShort => $subQuestions) {
            $this->info("Adding questions to {$stepShort}..");
            $step = Step::findByShort($stepShort);
            $orderForSubQuestions = 0;
            foreach ($subQuestions as $subQuestionName => $subQuestionData) {

                $subStep = SubStep::create([
                    'name' => ['nl' => $subQuestionName],
                    'order' => $orderForSubQuestions,
                    'step_id' => $step->id,
                    'sub_step_template_id' => $subQuestionData['sub_step_template_id'],
                ]);

                foreach ($subQuestionData['questions'] as $questionData) {
                    // create the question itself
                    $questionData['name'] = [
                        'nl' => __($questionData['translation'] . '.title'),
                    ];
                    $questionData['help_text'] = [
                        'nl' => __($questionData['translation'] . '.help'),
                    ];

                    $toolQuestion = ToolQuestion::create(
                        Arr::except($questionData, ['tool_question_values'])
                    );

                    $subStep->toolQuestions()->attach($toolQuestion, ['order' => $orderForSubQuestions]);

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
                    // now we have to create the morph relationship
                }

                $orderForSubQuestions++;
            }
        }
    }
}
