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
    public ?SubStep $subStep = null;
    public ?Questionnaire $questionnaire = null;

    protected array $skipSubSteps = [];

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

    public function skipSubStep(SubStep $subStep): self
    {
        $this->skipSubSteps[] = $subStep->id;
        $this->skipSubSteps = array_unique($this->skipSubSteps);
        return $this;
    }

    /**
     * Check if we should incomplete steps because conditional steps have come free, or if we need to
     * incomplete sub steps because they are hidden now.
     */
    public function checkConditionals(array $filledInAnswers)
    {
        Log::debug("Checking conditionals..");
        $building = $this->building;
        $currentInputSource = $this->inputSource;
        // We must do it for the master also because we're not using model events
        $masterInputSource = $this->masterInputSource;

        $subStepsRelated = SubStep::where(function ($query) use ($filledInAnswers) {
            $query->whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""]);
            foreach ($filledInAnswers as $toolQuestionShort => $answer) {
                $query->orWhereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestionShort}\""]);
            }
        })
            ->whereNotIn('id', $this->skipSubSteps)
            ->get();

        $subSteppableRelated = SubSteppable::where(function ($query) use ($filledInAnswers) {
            $query->whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""]);
            foreach ($filledInAnswers as $toolQuestionShort => $answer) {
                $query->orWhereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestionShort}\""]);
            }
        })
            ->where('sub_steppable_type', ToolQuestion::class)
            ->whereNotIn('sub_step_id', $this->skipSubSteps)
            ->get();

        $evaluator = ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource);

        $stepsToCheck = [];
        $processedSubSteps = [];

        // The logic is as follows:
        // If a SubStep can be seen, and has all answers answered, we will complete it/keep it complete, and we will
        // check the Step because it might now be completable.
        // If a SubStep can be seen, but is missing some answers, we will incomplete it and/or the related Step.
        // If a SubStep cannot be seen, and it's complete, we incomplete it, and we will check the Step because it
        // might be now completable.

        foreach ($subStepsRelated as $subStep) {
            if ($evaluator->evaluate($subStep->conditions)) {
                // The SubStep is visible
                if ($this->hasAnsweredSubStep($subStep, $evaluator)) {
                    Log::debug("Completing SubStep {$subStep->name} because it has answers.");
                    SubStepHelper::complete($subStep, $building, $currentInputSource);
                    SubStepHelper::complete($subStep, $building, $masterInputSource);
                    $stepsToCheck[] = $subStep->step->id;
                } else {
                    Log::debug("Incompleting SubStep {$subStep->name} and Step {$subStep->step->name} because SubStep is missing answers.");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    SubStepHelper::incomplete($subStep, $building, $masterInputSource);
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $masterInputSource);
                }
            } else {
                $completedSubStep = CompletedSubStep::allInputSources()
                    ->forInputSource($masterInputSource)
                    ->forBuilding($building)
                    ->where('sub_step_id', $subStep->id)
                    ->first();

                // If it's an invisible step that is complete, we want to incomplete it.
                if ($completedSubStep instanceof CompletedSubStep) {
                    Log::debug("Incompleting sub step {$subStep->name} because it's not visible.");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    SubStepHelper::incomplete($subStep, $building, $masterInputSource);
                }

                // Add to array so we can check the Step completion later
                $stepsToCheck[] = $subStep->step->id;
            }

            $processedSubSteps[] = $subStep->id;
        }

        // The logic is as follows:
        // If a ToolQuestion can be viewed, and it has no answer, we want to incomplete its SubStep, and the related
        // Step.
        // If a ToolQuestion can be viewed, and it has an answer, we want to check the other answers, and if
        // all answers are filled, we will complete the SubStep/keep it complete, and check the Step.
        // If a ToolQuestion can not be viewed, we will again check the SubStep with the same logic as above.
        // SubSteps already processed will be skipped.

        foreach ($subSteppableRelated as $toolQuestionSubSteppable) {
            $subStep = $toolQuestionSubSteppable->subStep;

            // Skip if already processed
            if (! in_array($subStep->id, $processedSubSteps)) {
                $completedSubStep = CompletedSubStep::allInputSources()
                    ->forInputSource($masterInputSource)
                    ->forBuilding($building)
                    ->where('sub_step_id', $subStep->id)
                    ->first();

                if ($evaluator->evaluate($toolQuestionSubSteppable->conditions)) {
                    // The ToolQuestion is visible. Let's check if it has an answer.



                    if ($completedSubStep instanceof CompletedSubStep) {
                        Log::debug("Incompleting step {$subStep->step->name} line 157");
                        StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                        StepHelper::incomplete($subStep->step, $building, $masterInputSource);

                        Log::debug("Incompleting sub step {$subStep->name} line 161");
                        SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                        SubStepHelper::incomplete($subStep, $building, $masterInputSource);
                    }
                } else {
                    if ($this->hasAnsweredSubStep()) {
                        Log::debug("Completing sub step {$subStep->name}");
                        SubStepHelper::complete($subStep, $building, $currentInputSource);
                        SubStepHelper::complete($subStep, $building, $masterInputSource);

                        if (! array_key_exists($subStep->step->id, $stepsToCheck)) {
                            $stepsToCheck[$subStep->step->id] = $subStep->step;
                        }
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
            $nextSubStep = $this->step->subSteps()->where('order', '>',
                $this->subStep->order)->orderBy('order')->first();
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
            $incompleteSubSteps = SubStepHelper::getIncompleteSubSteps($this->building, $nextStep,
                $this->masterInputSource);
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
                $nextUrl = route('cooperation.frontend.tool.quick-scan.index',
                    ['cooperation' => $cooperation, 'step' => $nextStep, 'subStep' => $nextSubStep]);
            }
        } elseif ($nextStep instanceof Step && $nextQuestionnaire instanceof Questionnaire) {
            if ($nextQuestionnaire->step_id !== $nextStep->id) {
                // TODO: Temporary, remove if when no issues arise
                DiscordNotifier::init()->notify("Next questionnaire doesn't belong to next step! Step ID: {$nextStep->id}. Questionnaire ID: {$nextQuestionnaire->id}.");
                $nextUrl = '';
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index',
                    ['cooperation' => $cooperation, 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire]);
            }
        } else {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index', ['cooperation' => $cooperation]);
        }

        Log::debug($nextUrl);
        return $nextUrl;
    }

    private function hasAnsweredSubStep(SubStep $subStep, ConditionEvaluator $evaluator): bool
    {
        $questionsWithAnswers = 0;
        $visibleQuestions = 0;

        foreach ($subStep->toolQuestions as $toolQuestion) {
            /** @var SubSteppable $subSteppable */
            $subSteppable = $toolQuestion->pivot;
            if ($evaluator->evaluate($subSteppable->conditions ?? [])) {
                $visibleQuestions++;

                $answer = $this->building->getAnswer($this->masterInputSource, $toolQuestion);
                if (! empty($answer) || (is_numeric($answer) && (int) $answer === 0)) {
                    $questionsWithAnswers++;
                }
            }

            // Break early to ensure we don't do too many queries if not necessary
            if ($visibleQuestions !== $questionsWithAnswers) {
                break;
            }
        }

        return $questionsWithAnswers === $visibleQuestions;
    }
}