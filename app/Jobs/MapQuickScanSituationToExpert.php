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
use App\Services\ConditionService;
use App\Services\ToolQuestionService;
use Illuminate\Support\Facades\Log;

class MapQuickScanSituationToExpert extends NonHandleableJobAfterReset
{
    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public MeasureApplication $measureApplication;

    public function __construct(Building $building, InputSource $inputSource, MeasureApplication $measureApplication)
    {
        parent::__construct();
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->measureApplication = $measureApplication;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $boilerValues = Service::findByShort('boiler')->values;

        // Convert (heat pump) measure application scenario to expert scan.
        // Note: currently only triggered if a user gets a heat pump measure application in their action plan,
        // before they have completed the heating step.
        $mapping = [
            'building-heating-application' => [
                'questions' => ['new-building-heating-application'],
                'answers' => [
                    'clauses' => [
                        [
                            'value' => 'none',
                            'operator' => Clause::CONTAINS,
                            'result' => null,
                        ],
                    ],
                ],
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
            'boiler-type' => [
                'questions' => ['new-boiler-type'],
                'answers' => [
                    'service' => 'boiler',
                    'column' => 'id',
                    'clauses' => [
                        [
                            'value' => $boilerValues->where('calculate_value', 1)->first()->id,
                            'operator' => Clause::EQ,
                            'result' => 'conventional',
                        ],
                        [
                            'value' => $boilerValues->where('calculate_value', 2)->first()->id,
                            'operator' => Clause::EQ,
                            'result' => 'improved-efficiency',
                        ],
                        [
                            'value' => $boilerValues->where('calculate_value', 3)->first()->id,
                            'operator' => Clause::EQ,
                            'result' => 'hr100',
                        ],
                        [
                            'value' => $boilerValues->where('calculate_value', 4)->first()->id,
                            'operator' => Clause::EQ,
                            'result' => 'hr104',
                        ],
                        [
                            'value' => $boilerValues->where('calculate_value', 5)->first()->id,
                            'operator' => Clause::EQ,
                            'result' => 'hr107',
                        ],
                        [
                            'value' => null,
                            'operator' => Clause::EQ,
                            'result' => 'hr107',
                        ],
                    ],
                ],
            ],
        ];

        $calculateValue = HeatPumpHelper::MEASURE_SERVICE_LINK[$this->measureApplication->short];
        // Due to high level intelligence we used the same shorts for the custom values as for the measure applications!
        $type = $this->measureApplication->short;
        $source = $calculateValue <= 3 ? ['hr-boiler', 'heat-pump'] : ['heat-pump'];

        $currentHeatSourceWarmTapWater = $this->building->getAnswer($this->masterInputSource, ToolQuestion::findByShort('heat-source-warm-tap-water'));
        if (in_array('hr-boiler', $source)) {
            $newHeatSourceWarmTapWater = empty($currentHeatSourceWarmTapWater)
                ? ['hr-boiler']
                : $currentHeatSourceWarmTapWater;
            if (false !== ($index = array_search('none', $newHeatSourceWarmTapWater))) {
                // Replace none with hr-boiler
                $newHeatSourceWarmTapWater = array_replace($newHeatSourceWarmTapWater, [$index => 'hr-boiler']);
            }
        } else {
            $newHeatSourceWarmTapWater = ['heat-pump'];
        }

        $this->saveAnswer(ToolQuestion::findByShort('new-heat-pump-type'), $type);
        // It is important to first map the new-heat-source, else the potential answer for new-heat-source-warm-tap-water
        // might be removed due to conditionals.
        $this->saveAnswer(ToolQuestion::findByShort('new-heat-source'), $source);
        $this->saveAnswer(ToolQuestion::findByShort('new-heat-source-warm-tap-water'), $newHeatSourceWarmTapWater);

        // Force replace
        $this->saveAnswer(ToolQuestion::findByShort('heat-pump-replace'), true);

        $boilerAgeQuestion = ToolQuestion::findByShort('boiler-placed-date');
        $viewable = ConditionService::init()->building($this->building)->inputSource($this->masterInputSource)
            ->forModel($boilerAgeQuestion)->isViewable();

        if ($viewable) {
            $answer = $this->building->getAnswer($this->masterInputSource, $boilerAgeQuestion);

            if (is_numeric($answer)) {
                $diff = now()->format('Y') - $answer;
                $this->saveAnswer(ToolQuestion::findByShort('hr-boiler-replace'), ! ($diff < 10));
            }
        }

        foreach ($mapping as $tqShort => $data) {
            $toolQuestion = ToolQuestion::findByShort($tqShort);

            $viewable = ConditionService::init()->building($this->building)->inputSource($this->masterInputSource)
                ->forModel($toolQuestion)->isViewable();

            $answer = $viewable ? $this->building->getAnswer($this->masterInputSource, $toolQuestion) : null;

            // Morph answer if needed
            if (array_key_exists('answers', $data)) {
                $answerStruct = $data['answers'];

                if (! is_null($answer)) {
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
        $answers['new-heat-pump-type'] = $type;

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
            case Clause::CONTAINS:
                if (is_array($answer)) {
                    return in_array($value, $answer);
                }
                return $answer == $value;
            default:
            case Clause::EQ:
                return $answer == $value;
        }
    }

    protected function resolveClauses(array $clauses, $answer)
    {
        foreach ($clauses as $clause) {
            if ($this->evaluateClause($clause, $answer)) {
                if (is_array($answer)) {
                    $index = array_search($clause['value'], $answer);
                    $answer[$index] = $clause['result'];
                    $answer = array_filter($answer);
                } else {
                    $answer = $clause['result'];
                }
                break;
            }
        }

        return $answer;
    }

    protected function saveAnswer(ToolQuestion $question, $answer)
    {
        Log::debug("Mapping " . (is_array($answer) ? json_encode($answer) : $answer) . " for question {$question->short}");

        ToolQuestionService::init()
            ->toolQuestion($question)
            ->building($this->building)
            ->currentInputSource($this->inputSource)
            ->save($answer);
    }
}
