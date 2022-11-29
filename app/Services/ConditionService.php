<?php

namespace App\Services;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Alert;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestionCustomValue;
use App\Models\ToolQuestionValuable;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConditionService
{
    use FluentCaller;

    protected Model $model;
    protected Building $building;
    protected InputSource $inputSource;

    public function forModel(Model $model): self
    {
        // TODO: Should we check for supported classes or just expect the developers to not be retarded?
        $this->model = $model;
        return $this;
    }

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function inputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function hasCompletedSteps(array $steps): bool
    {
        return ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->inputSource)
            ->evaluate([
                [
                    [
                        'column' => 'fn',
                        'operator' => 'HasCompletedStep',
                        'value' => [
                            'steps' => $steps,
                            'input_source_shorts' => [
                                InputSource::RESIDENT_SHORT,
                                InputSource::COACH_SHORT,
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function isViewable(?Collection $answers = null): bool
    {
        $answers = is_null($answers) ? collect() : $answers;

        $selfConditionsOnly = [
            SubStep::class, ToolQuestionCustomValue::class, ToolQuestionValuable::class, Alert::class,
        ];

        $class = get_class($this->model);
        $evaluator = ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->inputSource);

        if (in_array($class, $selfConditionsOnly)) {
            $conditions = $this->model->conditions ?? [];
        } elseif ($class === SubSteppable::class) {
            $conditions = $this->model->conditions ?? [];
            $conditions = array_merge($conditions, $this->model->subStep->conditions ?? []);
        } else {
            // So it isn't a simple case. We will have to iterate. In this case it's a ToolQuestion or
            // ToolCalculationResult. We fetch all SubSteps and SubSteppables to check their conditions.
            $subSteps = $this->model->subSteps()
                ->where('sub_steps.conditions', '!=', null)
                ->where('sub_steps.conditions', '!=', DB::raw("cast('[]' as json)"))
                ->get();

            $subSteppables = $this->model->subSteps()
                ->wherePivot('conditions', '!=', null)
                ->wherePivot('conditions', '!=', DB::raw("cast('[]' as json)"))
                ->get();

            // First fetch all conditions, so we can retrieve any required related answers in one go
            $conditionsForAllEvaluatable = [];
            foreach ($subSteps as $subStep) {
                $conditionsForAllEvaluatable = array_merge($conditionsForAllEvaluatable, $subStep->conditions);
            }
            foreach ($subSteppables as $subSteppable) {
                $conditionsForAllEvaluatable = array_merge($conditionsForAllEvaluatable, $subSteppable->pivot->conditions);
            }
            $answersForAllEvaluatable = $evaluator->getToolAnswersForConditions($conditionsForAllEvaluatable, $answers);

            // We sadly can't use one big array for evaluation; consider the case that both a SubStep and SubSteppable
            // have conditions; it might be that the SubSteppable passes, but the SubStep does not. It however does
            // create a truthy OR statement. We don't want that, so we will evaluate per SubStep(pable), and combine
            // if needed...
            $passes = false;
            foreach ($subSteps as $subStep) {
                $conditions = $subStep->conditions;
                $passes = $evaluator->evaluateCollection($conditions, $answersForAllEvaluatable);

                if ($passes) {
                    if (! empty($subStep->pivot->conditions)) {
                        $conditions = $subStep->pivot->conditions;
                        $passes = $evaluator->evaluateCollection($conditions, $answersForAllEvaluatable);

                        if ($passes) {
                            // We're done here
                            break;
                        }
                    } else {
                        // We're done here
                        break;
                    }
                }
            }

            if (! $passes) {
                // We haven't passed yet, so we will check the SubSteppables
                foreach ($subSteppables as $subSteppable) {
                    $conditions = $subSteppable->pivot->conditions;
                    $passes = $evaluator->evaluateCollection($conditions, $answersForAllEvaluatable);

                    if ($passes) {
                        if (! empty($subStep->conditions)) {
                            $conditions = $subStep->conditions;
                            $passes = $evaluator->evaluateCollection($conditions, $answersForAllEvaluatable);

                            if ($passes) {
                                // We're done here
                                break;
                            }
                        } else {
                            // We're done here
                            break;
                        }
                    }
                }
            }

            // In the scenario that NONE of the SubSteppables or SubSteps have conditions, passes will remain false.
            // We will know if both collections are empty.
            if (! $passes) {
                $passes = $subSteps->count() === 0 && $subSteppables->count() === 0;
            }

            return $passes;
        }

        $answers = $evaluator->getToolAnswersForConditions($conditions, $answers);

        return $evaluator->evaluateCollection($conditions, $answers);
    }
}