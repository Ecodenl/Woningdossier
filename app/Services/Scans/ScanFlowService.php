<?php

namespace App\Services\Scans;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\StepHelper;
use App\Helpers\SubStepHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;
use App\Services\DiscordNotifier;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\Log;

class ScanFlowService
{
    use FluentCaller;

    public Step $step;
    public Scan $scan;
    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public ?SubStep $subStep;
    public ?Questionnaire $questionnaire;

    public function __construct(Scan $scan, Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->scan = $scan;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
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
        $masterInputSource = $this->masterInputSource;

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
                    ->where('sub_step_id', '!=', $this->subStep->id)
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
                ->where('sub_step_id', '!=', $this->subStep->id)
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
                    Log::debug("Incompleting step {$subStep->step->name} line 125");
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);
                }
            } else {
                // If it's an invisible step that is complete, we want to incomplete it.
                if ($completedSubStep instanceof CompletedSubStep) {
                    Log::debug("Incompleting sub step {$subStep->name} line 132");
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
                    Log::debug("Incompleting step {$subStep->step->name} line 157");
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);

                    Log::debug("Incompleting sub step {$subStep->name} line 161");
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
                    Log::debug("Completing sub step {$subStep->name}");
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
            Log::debug("Completing step {$step->name}");
            StepHelper::completeStepIfNeeded($step, $building, $currentInputSource, false);
        }
    }

    public function resolveNextUrl(): string
    {
        $nextStep = $this->step;
        $nextSubStep = null;
        $nextQuestionnaire = null;

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
                $nextQuestionnaire = $potentialQuestionnaire;
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

        if (! $nextStep instanceof Step) {
            Log::debug("No next step, fetching first in complete step..");
            // No next step set, let's see if there are any steps left incomplete
            $nextStep = $this->building->getFirstIncompleteStep([], $this->masterInputSource);
        }

        // There are incomplete steps left, set the sub step
        if ($nextStep instanceof Step) {
            // retrieve all incomplete sub steps for the building
            $incompleteSubSteps = SubStepHelper::getIncompleteSubSteps($this->building, $nextStep, $this->masterInputSource);
            foreach ($incompleteSubSteps as $subStep) {
                if ($this->building->user->account->can('show', [$subStep, $this->building])) {
                    $nextSubStep = $subStep;
                    break;
                }
            }
        }

        // For some reason the cooperation isn't automatically bound, probably because of Livewire.
        // For now, this has to stay.
        $cooperation = $this->building->user->cooperation;

        if ($nextStep instanceof Step && $nextSubStep instanceof SubStep) {
            if ($nextSubStep->step_id !== $nextStep->id) {
                // TODO: Temporary, remove if when no issues arise
                DiscordNotifier::init()->notify("Next sub step doesn't belong to next step! Step ID: {$nextStep->id}. Sub step ID: {$nextSubStep->id}.");
                $nextUrl = '';
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['cooperation' => $cooperation, 'step' => $nextStep, 'subStep' => $nextSubStep]);
            }
        } elseif ($nextStep instanceof Step && $nextQuestionnaire instanceof Questionnaire) {
            if ($nextQuestionnaire->step_id !== $nextStep->id) {
                // TODO: Temporary, remove if when no issues arise
                DiscordNotifier::init()->notify("Next questionnaire doesn't belong to next step! Step ID: {$nextStep->id}. Questionnaire ID: {$nextQuestionnaire->id}.");
                $nextUrl = '';
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['cooperation' => $cooperation, 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire]);
            }
        } else {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index', ['cooperation' => $cooperation]);
        }

        Log::debug($nextUrl);
        return $nextUrl;
    }
}