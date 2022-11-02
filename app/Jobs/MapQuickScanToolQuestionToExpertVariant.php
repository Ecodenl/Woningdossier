<?php

namespace App\Jobs;

use App\Helpers\Conditions\Clause;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Services\ConditionService;
use App\Services\ToolQuestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MapQuickScanToolQuestionToExpertVariant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public ToolQuestion $toolQuestion;
    public $givenAnswer;
    public ConditionService $conditionService;

    public function __construct(Building $building, InputSource $inputSource, ToolQuestion $toolQuestion, $givenAnswer)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->toolQuestion = $toolQuestion;
        $this->givenAnswer = $givenAnswer;

        $this->conditionService = ConditionService::init()
            ->building($building)
            ->inputSource($inputSource)
            ->forModel($toolQuestion);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // We simply ONLY map what we were told to map, else it gets WAY too complex
        $mapping = [
            'heat-pump-type' => [
                'questions' => ['new-heat-source', 'new-heat-source-warm-tap-water'],
                'answers' => [
                    'service' => 'heat-pump',
                    'column' => 'calculate_value',
                    'clauses' => [
                        [
                            'operator' => Clause::LTE,
                            'value' => 3,
                            'result' => ['hr-boiler', 'heat-pump'],
                        ],
                        [
                            'operator' => Clause::GT,
                            'value' => 3,
                            'result' => ['heat-pump'],
                        ],
                    ],
                ],
                'steps' => ['heating'],
            ],
            'building-heating-application' => [
                'questions' => ['new-building-heating-application'],
                'steps' => ['heating'],
            ],
            'boiler-setting-comfort-heat' => [
                'questions' => ['new-boiler-setting-comfort-heat'],
                'answers' => [
                    'clauses' => [
                        [
                            'value' => 'unsure',
                            'operator' => Clause::EQ,
                            'result' => 'temp-high',
                        ],
                    ],
                ],
                'steps' => ['heating'],
            ],
            'water-comfort' => [
                'questions' => ['new-water-comfort'],
                'steps' => ['heating'],
            ],
            'cook-type' => [
                'questions' => ['new-cook-type'],
                'steps' => ['heating'],
            ],
            'interested-in-heat-pump-variant' => [
                'questions' => ['custom'],
            ],
        ];

        $tqShort = $this->toolQuestion->short;

        if (array_key_exists($tqShort, $mapping) && ! is_null($this->givenAnswer)) {
            $data = $mapping[$tqShort];
            if (! $this->conditionService->hasCompletedSteps($data['steps'])) {
                Log::debug("Proceeding to map answer for {$tqShort} because user has not yet finished step(s) " . implode(', ',
                        $data['steps']));

                $answer = $this->givenAnswer;

                // Morph answer if needed
                // TODO: This does not work with arrays yet but this is not relevant ATM
                if (array_key_exists('answers', $data)) {
                    $answerStruct = $data['answers'];
                    if (array_key_exists('service', $answerStruct)) {
                        // We now know we have a service value which we need
                        $service = Service::findByShort($answerStruct['service']);
                        $answer = $service->values()->where('id', $answer)->first();
                    }

                    if (array_key_exists('column', $answerStruct)) {
                        $answer = $answer->{$answerStruct['column']};
                    }

                    $clauses = $answerStruct['clauses'];
                    $answer = $this->resolveClauses($clauses, $answer);
                }

                foreach ($data['questions'] as $questionShort) {
                    if ($questionShort === 'custom') {
                        switch ($tqShort) {
                            case 'interested-in-heat-pump-variant':
                                $newPump = ToolQuestion::findByShort('new-heat-pump-type');
                                $heatSource = ToolQuestion::findByShort('new-heat-source');
                                $heatSourceWater = ToolQuestion::findByShort('new-heat-source-warm-tap-water');

                                if ($answer == 'full-heat-pump') {
                                    $type = 'full-heat-pump-outside-air';
                                    $source = ['heat-pump'];
                                } elseif ($answer == 'hybrid-heat-pump') {
                                    $type = 'hybrid-heat-pump-outside-air';
                                    $source = ['hr-boiler', 'heat-pump'];
                                } else {
                                    $temp = $this->building->getAnswer(
                                        $this->masterInputSource,
                                        ToolQuestion::findByShort('boiler-setting-comfort-heat')
                                    );

                                    if ($temp === 'temp-low') {
                                        $type = 'full-heat-pump-outside-air';
                                        $source = ['heat-pump'];
                                    } else {
                                        $type = 'hybrid-heat-pump-outside-air';
                                        $source = ['hr-boiler', 'heat-pump'];
                                    }
                                }

                                $this->saveAnswer($newPump, $type);
                                $this->saveAnswer($heatSource, $source);
                                $this->saveAnswer($heatSourceWater, $source);
                                break;
                        }
                    } else {
                        $question = ToolQuestion::findByShort($questionShort);
                        $this->saveAnswer($question, $answer);
                    }
                }
            }
        }
    }

    protected function evaluateClause(array $clause, $answer): bool
    {
        // TODO: If this expands drastically, we will want to use the ConditionEvaluator instead
        // For now we use a simple shell variant

        extract($clause);
        /**
         * @var $value
         * @var string $operator
         * @var $result
         */

        switch ($operator) {
            case Clause::GT:
                return $answer > $value;
            case Clause::LTE:
                return $answer <= $value;
            default:
            case Clause::EQ:
                return $answer == $value;
        }
    }

    protected function resolveClauses(array $clauses, $answer)
    {
        foreach ($clauses as $clause) {
            if ($this->evaluateClause($clause, $answer)) {
                $answer = $clause['result'];
                break;
            }
        }

        return $answer;
    }

    protected function saveAnswer(ToolQuestion $question, $answer)
    {
        Log::debug("Mapping " . is_array($answer) ? json_encode($answer) : $answer . " for question {$question->short}");

        ToolQuestionService::init($question)
            ->building($this->building)
            ->currentInputSource($this->inputSource)
            ->save($answer);
    }
}
