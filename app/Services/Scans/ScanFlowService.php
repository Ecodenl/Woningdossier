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
use App\Traits\RetrievesAnswers;
use Illuminate\Support\Facades\Log;

class ScanFlowService
{
    use FluentCaller,
        RetrievesAnswers;

    public Step $step;
    public Scan $scan;
    public InputSource $currentInputSource;
    public ?SubStep $subStep = null;
    public ?Questionnaire $questionnaire = null;

    protected array $skipSubSteps = [];

    public function __construct(Scan $scan, Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->currentInputSource = $inputSource;
        $this->scan = $scan;
        $this->inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
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
        $currentInputSource = $this->currentInputSource;
        // We must do it for the master also because we're not using model events
        $masterInputSource = $this->inputSource;

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
            ->whereNotIn('sub_step_id', $subStepsRelated->pluck('id')->toArray())
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
                    $stepsToCheck[] = $subStep->step->short;
                } else {
                    Log::debug("Incompleting SubStep {$subStep->name} and Step {$subStep->step->name} because SubStep is missing answers.");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                }
            } else {
                $completedSubStep = CompletedSubStep::allInputSources()
                    ->forInputSource($masterInputSource)
                    ->forBuilding($building)
                    ->where('sub_step_id', $subStep->id)
                    ->first();

                // If it's an invisible step that is complete, we want to incomplete it.
                if ($completedSubStep instanceof CompletedSubStep) {
                    Log::debug("Incompleting SubStep {$subStep->name} because it's not visible.");
                    SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                }

                // Add to array so we can check the Step completion later
                $stepsToCheck[] = $subStep->step->short;
            }

            $processedSubSteps[] = $subStep->id;
        }

        // The logic is as follows:
        // We will simply check if the related SubStep has answers or not.

        foreach ($subSteppableRelated as $toolQuestionSubSteppable) {
            $subStep = $toolQuestionSubSteppable->subStep;

            // Skip if already processed
            if (! in_array($subStep->id, $processedSubSteps)) {
                if ($evaluator->evaluate($subStep->conditions)) {
                    if ($this->hasAnsweredSubStep($subStep, $evaluator)) {
                        Log::debug("Completing SubStep {$subStep->name} because it has answers.");
                        SubStepHelper::complete($subStep, $building, $currentInputSource);
                        $stepsToCheck[] = $subStep->step->short;
                    } else {
                        Log::debug("Incompleting SubStep {$subStep->name} and Step {$subStep->step->name} because SubStep is missing answers.");
                        SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                        StepHelper::incomplete($subStep->step, $building, $currentInputSource);
                    }
                } else {
                    $completedSubStep = CompletedSubStep::allInputSources()
                        ->forInputSource($masterInputSource)
                        ->forBuilding($building)
                        ->where('sub_step_id', $subStep->id)
                        ->first();

                    // If it's an invisible step that is complete, we want to incomplete it.
                    if ($completedSubStep instanceof CompletedSubStep) {
                        Log::debug("Incompleting SubStep {$subStep->name} because it's not visible.");
                        SubStepHelper::incomplete($subStep, $building, $currentInputSource);
                    }

                    // Add to array so we can check the Step completion later
                    $stepsToCheck[] = $subStep->step->short;
                }

                $processedSubSteps[] = $subStep->id;
            }
        }

        // Finally, we loop through the Steps and complete them if needed
        $stepsToCheck = array_unique($stepsToCheck);
        foreach ($stepsToCheck as $stepShort) {
            $step = Step::findByShort($stepShort);
            Log::debug("Completing Step {$step->name} if possible");
            $completed = StepHelper::completeStepIfNeeded($step, $building, $currentInputSource, false);
            if (! $completed) {
                Log::debug("Step {$step->name} could not be completed, so we incomplete it.");
                StepHelper::incomplete($step, $building, $currentInputSource);
            }
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
            $nextStep = $this->building->getFirstIncompleteStep($this->inputSource);
        }

        // There are incomplete steps left, set the sub step
        if (! $nextSubStep instanceof SubStep && $nextStep instanceof Step) {
            // retrieve all incomplete sub steps for the building
            $incompleteSubSteps = SubStepHelper::getIncompleteSubSteps($this->building, $nextStep,
                $this->inputSource);
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
                // If it's visible, we will check if it's required. If it's not required, it doesn't matter after all
                if (in_array('required', $toolQuestion->validation)) {
                    $visibleQuestions++;

                    $answer = $this->getAnswer($toolQuestion->short, false);
                    if (! empty($answer) || (is_numeric($answer) && (int) $answer === 0)) {
                        $questionsWithAnswers++;
                    }
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