<?php

namespace App\Console\Commands;

use App\Models\BuildingType;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubStepTemplate;
use App\Models\SubStepToolQuestion;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\ToolQuestionValueable;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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

        $templateDefault = SubStepTemplate::findByShort('template-default');

        $structure = [
            'building-data' => [
                // sub step name
                'Wat voor woning' => [
                    // question data
                    'sub_step_template_id' => $templateDefault->id,
                    'questions' => [
                        [
                            'save_in' => 'building_features.building_type_id',
                            'translation' =>
                                'cooperation/tool/general-data/building-characteristics.index.building-type',
                            'tool_question_type_id' => $radioIconType->id,
                            'tool_question_values' => $buildingTypes,
                        ],
                    ],
                ],
            ],
        ];
        foreach ($structure as $stepShort => $subQuestions) {
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
                            $toolQuestion->toolQuestionValueables()->create(
                                [
                                    'order' => $toolQuestionValueOrder,
                                    'show' => true,
                                    'tool_question_valueable_type' => get_class($toolQuestionValue),
                                    'tool_question_valueable_id' => $toolQuestionValue->id,
                                ]
                            );
                        }
                    }
                    // now we have to create the morph relationship
                }

                $orderForSubQuestions++;
            }
        }
    }
}
