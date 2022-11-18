<?php

namespace App\Jobs;

use App\Calculations\HeatPump;
use App\Helpers\Conditions\Clause;
use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MapQuickScanSituationToExpert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public MeasureApplication $measureApplication;

    public function __construct(Building $building, InputSource $inputSource, MeasureApplication $measureApplication)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->measureApplication = $measureApplication;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Convert (heat-pump) measure application scenario to expert 
        $mapping = [
            'building-heating-application' => [
                'questions' => ['new-building-heating-application'],
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
            ],
            'water-comfort' => [
                'questions' => ['new-water-comfort'],
                'answers' => [
                    'model' => ComfortLevelTapWater::class,
                    'column' => 'calculate_value',
                    'clauses' => [
                        [
                            'value' => 1,
                            'operator' => Clause::EQ,
                            'result' => 'standard',
                        ],
                        [
                            'value' => 2,
                            'operator' => Clause::EQ,
                            'result' => 'comfortable',
                        ],
                        [
                            'value' => 3,
                            'operator' => Clause::EQ,
                            'result' => 'extra-comfortable',
                        ],
                    ],
                ],
            ],
            'cook-type' => [
                'questions' => ['new-cook-type'],
            ],
        ];

        $calculateValue = HeatPumpHelper::MEASURE_SERVICE_LINK[$this->measureApplication->short];
        // Due to high level intelligence we used the same shorts for the custom values as for the measure applications!
        $type = $this->measureApplication->short;
        $source = $calculateValue <= 3 ? ['hr-boiler', 'heat-pump'] : ['heat-pump'];

        $this->saveAnswer(ToolQuestion::findByShort('new-heat-pump-type'), $type);
        $this->saveAnswer(ToolQuestion::findByShort('new-heat-source'), $source);
        $this->saveAnswer(ToolQuestion::findByShort('new-heat-source-warm-tap-water'), (in_array('hr-boiler', $source) ? ['hr-boiler'] : ['heat-pump']));

        foreach ($mapping as $tqShort => $data) {
            $toolQuestion = ToolQuestion::findByShort($tqShort);
            $answer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);

            // Morph answer if needed
            // TODO: This does not work with arrays yet but this is not relevant ATM
            if (array_key_exists('answers', $data)) {
                $answerStruct = $data['answers'];
                if (array_key_exists('service', $answerStruct)) {
                    // We now know we have a service value which we need
                    $service = Service::findByShort($answerStruct['service']);
                    $answer = $service->values()->where('id', $answer)->first();
                } elseif (array_key_exists('model', $answerStruct)) {
                    $answer = (new $answerStruct['model'])->find($answer);
                }

                if (array_key_exists('column', $answerStruct)) {
                    $answer = $answer->{$answerStruct['column']};
                }

                $clauses = $answerStruct['clauses'];
                $answer = $this->resolveClauses($clauses, $answer);
            }

            foreach ($data['questions'] as $questionShort) {
                $question = ToolQuestion::findByShort($questionShort);
                $this->saveAnswer($question, $answer);
            }
        }

        // As last step, calculate required power to save as desired power...........
        $answers['new-heat-pump-type'] = ToolQuestion::findByShort('new-heat-pump-type')
            ->toolQuestionCustomValues()
            ->where('extra->calculate_value', $calculateValue)
            ->first()->short;

        // If we're not in expert yet, we need the heating temp for the calculations
        $heatingTemp = $this->building->getAnswer($this->masterInputSource, ToolQuestion::findByShort('boiler-setting-comfort-heat'));
        // We assume the worst if the user doesn't know
        $answers['new-boiler-setting-comfort-heat'] = $heatingTemp === 'unsure' ? 'temp-high' : $heatingTemp;
        // Force 0 to have the desired power calculated
        $answers['heat-pump-preferred-power'] = 0;

        $results = HeatPump::calculate($this->building, $this->inputSource, collect($answers));
        $this->saveAnswer(ToolQuestion::findByShort('heat-pump-preferred-power'), $results['advised_system']['desired_power']);
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
        Log::debug("Mapping " . (is_array($answer) ? json_encode($answer) : $answer) . " for question {$question->short}");

        ToolQuestionService::init($question)
            ->building($this->building)
            ->currentInputSource($this->inputSource)
            ->save($answer);
    }
}
