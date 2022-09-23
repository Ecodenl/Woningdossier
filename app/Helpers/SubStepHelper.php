<?php

namespace App\Helpers;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;

class SubStepHelper
{
    /**
     * Complete a sub step for a building.
     *
     * @param \App\Models\SubStep $subStep
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     *
     * @return void
     */
    public static function complete(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        CompletedSubStep::allInputSources()->firstOrCreate([
            'sub_step_id' => $subStep->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ]);
    }

    /**
     * Incomplete a step for a building.
     *
     * @param \App\Models\SubStep $subStep
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     *
     * @return void
     * @throws \Exception
     */
    public static function incomplete(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        CompletedSubStep::allInputSources()->where([
            'sub_step_id' => $subStep->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ])->delete();
    }

    /**
     * Check if we should incomplete steps because conditional steps have come free, or if we need to
     * incomplete sub steps because they are hidden now.
     *
     * @param \App\Models\CompletedSubStep $completedSubStep
     *
     * @return void
     * @throws \Exception
     */
    public static function checkConditionals(CompletedSubStep $completedSubStep)
    {
        // TODO: We must find a way to make this faster

        $building = $completedSubStep->building;
        $currentInputSource = $completedSubStep->inputSource;
        // We must do it for the master also because we're not using model events
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $subStepCompleted = $completedSubStep->subStep;
        $subStepsRelated = [];
        $toolQuestionsRelated = [];

        foreach ($subStepCompleted->toolQuestions as $toolQuestion) {
            $subStepsRelated = array_merge($subStepsRelated,
                SubStep::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestion->short}\""])
                    ->pluck('id')->toArray()
            );
            $toolQuestionsRelated = array_merge($toolQuestionsRelated,
                SubSteppable::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestion->short}\""])
                    ->where('sub_steppable_type', ToolQuestion::class)
                    ->pluck('id')->toArray()
            );
        }

        // Also add sub steps with custom evaluators
        $subStepsRelated = array_merge($subStepsRelated,
            SubStep::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""])
                ->pluck('id')->toArray()
        );
        $toolQuestionsRelated = array_merge($toolQuestionsRelated,
            SubSteppable::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""])
                ->where('sub_steppable_type', ToolQuestion::class)
                ->pluck('id')->toArray()
        );

        $subStepsRelated = array_unique($subStepsRelated);
        $toolQuestionsRelated = array_unique($toolQuestionsRelated);
        $subSteps = SubStep::findMany($subStepsRelated);
        $toolQuestionSubSteppables = SubSteppable::findMany($toolQuestionsRelated);

        $evaluator = ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource);

        $stepsToCheck = [];

        foreach ($subSteps as $subStep) {
            $completedSubStep = CompletedSubStep::allInputSources()
                ->forInputSource($masterInputSource)
                ->forBuilding($building)
                ->where('sub_step_id', $subStep->id)
                ->first();

            if ($evaluator->evaluate($subStep->conditions)) {
                // If it's a visible step that is not complete, we want the parent step to also to also not be
                // complete.
                if (!$completedSubStep instanceof CompletedSubStep) {
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);
                }
            } else {
                // If it's an invisible step that is complete, we want to incomplete it.
                if ($completedSubStep instanceof CompletedSubStep) {
                    static::incomplete($subStep, $building, $currentInputSource);
                    static::incomplete($subStep, $building, $masterInputSource);
                }

                // Add to array if not already there so we can check the step completion later
                if (!array_key_exists($subStep->step->id, $stepsToCheck)) {
                    $stepsToCheck[$subStep->step->id] = $subStep->step;
                }
            }
        }

        foreach ($toolQuestionSubSteppables as $toolQuestionSubSteppable) {
            $subStep = $toolQuestionSubSteppable->subStep;

            $completedSubStep = CompletedSubStep::allInputSources()
                ->forInputSource($masterInputSource)
                ->forBuilding($building)
                ->where('sub_step_id', $subStep->id)
                ->first();

            if ($evaluator->evaluate($toolQuestionSubSteppable->conditions)) {
                // If the conditions now match and the sub step was completed, we want to incomplete both the step
                // and sub step
                if ($completedSubStep instanceof CompletedSubStep) {
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);

                    static::incomplete($subStep, $building, $currentInputSource);
                    static::incomplete($subStep, $building, $masterInputSource);
                }
            } else {
                // If the other questions have answers, we want to re-complete the sub step
                $questionsWithAnswers = 0;
                $visibleQuestions = 0;

                foreach ($subStep->toolQuestions as $toolQuestion) {
                    /** @var SubSteppable $subSteppable */
                    $subSteppable = $toolQuestion->pivot;
                    if ($evaluator->evaluate($subSteppable->conditions ?? [])) {
                        $visibleQuestions++;

                        if (!empty($building->getAnswer($masterInputSource, $toolQuestion))) {
                            $questionsWithAnswers++;
                        }
                    }

                    // Break early to ensure we don't do too many queries if not necessary
                    if ($visibleQuestions !== $questionsWithAnswers) {
                        break;
                    }
                }

                if ($questionsWithAnswers === $visibleQuestions) {
                    static::complete($subStep, $building, $currentInputSource);
                    static::complete($subStep, $building, $masterInputSource);

                    if (!array_key_exists($subStep->step->id, $stepsToCheck)) {
                        $stepsToCheck[$subStep->step->id] = $subStep->step;
                    }
                }
            }
        }

        foreach ($stepsToCheck as $step) {
            // Check if we can complete the step if necessary
            StepHelper::completeStepIfNeeded($step, $building, $currentInputSource, false);
        }
    }
}
