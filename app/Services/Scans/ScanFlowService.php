<?php

namespace App\Services\Scans;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\StepHelper;
use App\Helpers\SubStepHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\Log;

class ScanFlowService
{
    use FluentCaller;

    public Step $step;
    public SubStep $subStep;
    public Building $building;
    public InputSource $inputSource;
    public Questionnaire $questionnaire;

    public function __construct(Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
    }

    public function forQuestionnaire(Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;
        return $this;
    }

    public function forStep(Step $step): self
    {
        $this->step = $step;
        return $this;
    }

    public function forSubStep(SubStep $subStep): self
    {
        $this->subStep = $subStep;
        return $this;
    }

    /**
     * Check if we should incomplete steps because conditional steps have come free, or if we need to
     * incomplete sub steps because they are hidden now.
     *
     */
    public function checkConditionals(array $toolQuestions)
    {
        Log::debug("Checking conditionals..");
        $building = $this->building;
        $currentInputSource = $this->inputSource;
        // We must do it for the master also because we're not using model events
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $subStepsRelated = [];
        $toolQuestionsRelated = [];

        foreach ($toolQuestions as $toolQuestion) {
            $subStepsRelated = array_merge($subStepsRelated,
                SubStep::whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestion->short}\""])
                    ->where('id', '!=', $this->subStep->id)
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
                ->where('id', '!=', $this->subStep->id)
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
                    Log::debug("Incompleting {$subStep->step->name} line 86");
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);
                }
            } else {
                // If it's an invisible step that is complete, we want to incomplete it.
                if ($completedSubStep instanceof CompletedSubStep) {
                    Log::debug("Incompleting {$subStep->name} line 93");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    SubStepHelper::incomplete($subStep, $building, $masterInputSource);
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
                    Log::debug("Incompleting {$subStep->name} line 118");
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);

                    Log::debug("Incompleting {$subStep->name} line 122");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    SubStepHelper::incomplete($subStep, $building, $masterInputSource);
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
                    SubStepHelper::complete($subStep, $building, $currentInputSource);
                    SubStepHelper::complete($subStep, $building, $masterInputSource);

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

    public function resolveNextStep()
    {
        if ($this->subStep instanceof SubStep) {
            $nextSubStep = $this->step->subSteps()->where('order', '>', $this->subStep->order)->orderBy('order')->first();
            // we will check if the current sub step is the last one, that way we know we have to go to the next one.
            $lastSubStepForStep = $this->step->subSteps()->orderByDesc('order')->first();
            if ($lastSubStepForStep->id === $this->subStep->id) {
                // Let's check if there's questionnaires left
                if ($this->step->hasActiveQuestionnaires()) {
                    $nextQuestionnaire = $this->step->questionnaires()->active()->orderBy('order')->first();
                } else {
                    $nextStep = $this->step->nextQuickScan();
                    // the last can't have a next one
                    if ($nextStep instanceof Step) {
                        // the previous step is a different one, so we should get the first sub step of the previous step
                        $nextSubStep = $nextStep->subSteps()->orderBy('order')->first();
                    }
                }
            }
        } elseif ($this->questionnaire instanceof Questionnaire) {
            // We're currently in a questionnaire. We need to check if the next button will be another questionnaire
            $potentialQuestionnaire = $this->step->questionnaires()->active()
                ->where('order', '>', $this->questionnaire->order)
                ->orderBy('order')->first();

            if ($potentialQuestionnaire instanceof Questionnaire) {
                $this->nextQuestionnaire = $potentialQuestionnaire;
            } else {
                // No more questionnaires, let's start the logic to get the next sub step
                $nextStep = $this->step->nextQuickScan();
                // the last can't have a next one
                if ($nextStep instanceof Step) {
                    // the previous step is a different one, so we should get the first sub step of the previous step
                    $nextSubStep = $nextStep->subSteps()->orderBy('order')->first();
                }
            }
        }

        if (!$nextStep instanceof Step) {
            // No next step set, let's see if there are any steps left incomplete
            $this->firstIncompleteStep = $this->building->getFirstIncompleteStep([$this->step->id], $this->masterInputSource);
        }

        // There are incomplete steps left, set the sub step
        if ($this->firstIncompleteStep instanceof Step) {
            $this->firstIncompleteSubStep = $this->building->getFirstIncompleteSubStep($this->firstIncompleteStep, [], $this->masterInputSource);
        }



        if ($nextStep instanceof \App\Models\Step && $nextSubStep instanceof \App\Models\SubStep) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $nextStep->scan, 'step' => $nextStep, 'subStep' => $nextSubStep]);
        } elseif ($nextStep instanceof \App\Models\Step && $nextQuestionnaire instanceof \App\Models\Questionnaire) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['scan' => $nextStep->scan, 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire]);
        } else {
            if ($firstIncompleteStep instanceof \App\Models\Step && $firstIncompleteSubStep instanceof \App\Models\SubStep) {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $firstIncompleteStep->scan, 'step' => $firstIncompleteStep, 'subStep' => $firstIncompleteSubStep]);
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index', ['scan' => $currentScan]);
            }
        }

        $this->nextUrl = $nextUrl ?? '';
    }

}