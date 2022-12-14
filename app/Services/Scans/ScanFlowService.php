<?php

namespace App\Services\Scans;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\StepHelper;
use App\Helpers\SubStepHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;
use App\Services\Models\QuestionnaireService;
use App\Services\Models\SubStepService;
use App\Traits\FluentCaller;
use App\Traits\RetrievesAnswers;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
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
    public Cooperation $cooperation;

    protected array $skipSubSteps = [];

    public function __construct(Scan $scan, Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->cooperation = $building->user->cooperation;
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

        $subStepService = SubStepService::init()
            ->building($building)
            ->inputSource($currentInputSource);

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

        // Get all conditions to get answers for
        $allConditions = [];
        foreach ($subStepsRelated as $subStep) {
            $allConditions = array_merge($allConditions, $subStep->conditions ?? []);
        }
        foreach ($subSteppableRelated as $subSteppable) {
            $allConditions = array_merge($allConditions, $subSteppable->conditions ?? []);
        }

        $evaluator = ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource);

        $evaluator->setAnswers($evaluator->getToolAnswersForConditions($allConditions));

        $stepsToCheck = [];
        $processedSubSteps = $this->evaluateSubSteps($subStepsRelated, $evaluator);

        // The logic is as follows:
        // We will simply check if the related SubStep has answers or not.

        foreach ($subSteppableRelated as $toolQuestionSubSteppable) {
            $subStep = $toolQuestionSubSteppable->subStep;

            // Skip if already processed
            if (! in_array($subStep->id, $processedSubSteps)) {
                if ($this->hasAnsweredSubStep($subStep, $evaluator)) {
                    Log::debug("Completing SubStep {$subStep->name} because it has answers.");
                    $subStepService->subStep($subStep)->complete();
                    $stepsToCheck[] = $subStep->step->short;
                } else {
                    Log::debug("Incompleting SubStep {$subStep->name} and Step {$subStep->step->name} because SubStep is missing answers.");
                    $subStepService->subStep($subStep)->incomplete();
                    StepHelper::incomplete($subStep->step, $building, $currentInputSource);
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

    /** Resolve the next url based on the current step and sub step */
    public function resolveNextUrl(): string
    {
        $nextStep = $this->step;
        $nextSubStep = null;
        $nextQuestionnaire = null;

        $questionnaireService = QuestionnaireService::init()
            ->cooperation($this->cooperation)
            ->step($this->step);

        if ($this->subStep instanceof SubStep) {
            $nextSubStep = $this->step->subSteps()
                ->where('order', '>', $this->subStep->order)
                ->orderBy('order')
                ->first();

            // we will check if the current sub step is the last one, that way we know we have to go to the next one.
            $lastSubStepForStep = $this->step->subSteps()->orderByDesc('order')->first();

            if ($lastSubStepForStep->id === $this->subStep->id) {
                // Let's check if there's questionnaires left
                if ($questionnaireService->hasActiveQuestionnaires()) {
                    $nextQuestionnaire = $questionnaireService
                        ->resolveQuestionnaire(true);
                } else {
                    // Unwanted behaviour for expert
                    if ($this->scan->short !== Scan::EXPERT) {
                        $nextStep = $this->step->nextStepForScan();

                        // the last can't have a next one
                        if ($nextStep instanceof Step) {
                            // the previous step is a different one, so we should get the first sub step of the previous step
                            $nextSubStep = $nextStep->subSteps()->orderBy('order')->first();
                        }
                    }
                }
            }
        } elseif ($this->questionnaire instanceof Questionnaire) {
            // We're currently in a questionnaire. We need to check if the next button will be another questionnaire
            $potentialQuestionnaire = $questionnaireService
                ->questionnaire($this->questionnaire)
                ->resolveQuestionnaire(true);

            if ($potentialQuestionnaire instanceof Questionnaire) {
                $nextQuestionnaire = $potentialQuestionnaire;
            } else {
                // Unwanted behaviour for expert
                if ($this->scan->short !== Scan::EXPERT) {
                    // No more questionnaires, let's start the logic to get the next sub step
                    $nextStep = $this->step->nextStepForScan();
                    // the last can't have a next one
                    if ($nextStep instanceof Step) {
                        // the previous step is a different one, so we should get the first sub step of the previous step
                        $nextSubStep = $nextStep->subSteps()->orderBy('order')->first();
                    }
                }
            }
        }

        if (! $nextStep instanceof Step) {
            Log::debug("No next step, fetching first in complete step..");
            // No next step set, let's see if there are any steps left incomplete
            $nextStep = $this->building->getFirstIncompleteStep($this->scan, $this->inputSource);
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

        $cooperation = $this->cooperation;

        if ($nextStep instanceof Step && $nextSubStep instanceof SubStep) {
            // TODO: This can't happen for Expert, should we build safety?
            $nextUrl = route("cooperation.frontend.tool.simple-scan.index", [
                'cooperation' => $cooperation, 'scan' => $this->scan, 'step' => $nextStep, 'subStep' => $nextSubStep
            ]);
        } elseif ($nextStep instanceof Step && $nextQuestionnaire instanceof Questionnaire) {
            if ($this->scan->short === Scan::EXPERT) {
                $nextUrl = route('cooperation.frontend.tool.expert-scan.questionnaires.index', [
                    'cooperation' => $cooperation, /*'scan' => $this->scan,*/ 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire
                ]);
            } else {
                $nextUrl = route('cooperation.frontend.tool.simple-scan.questionnaires.index', [
                    'cooperation' => $cooperation, 'scan' => $this->scan, 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire
                ]);
            }
        } else {
            $scan = $this->scan;
            if ($this->scan->short === Scan::EXPERT) {
                $scan = Scan::findByShort(Scan::QUICK);
            }

            $nextUrl = route('cooperation.frontend.tool.simple-scan.my-plan.index', [
                'cooperation' => $cooperation, 'scan' => $scan
            ]);
        }

        return $nextUrl;
    }

    /** Resolve the first url, based on the user his current progression */
    public function resolveInitialUrl(): string
    {
        // Initial URL is only for lite and quick scan

        $building = $this->building;
        $masterInputSource = $this->inputSource;
        $scan = $this->scan;

        // If the quick scan is complete, we just redirect to my plan
        if ($building->hasCompletedScan($scan, $masterInputSource)) {
            $url = route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'));
        } else {
            $mostRecentCompletedSubStep = $scan->subSteps()
                ->join('completed_sub_steps', function (JoinClause $join) use ($building, $masterInputSource) {
                    $join
                        ->on('sub_steps.id', '=', 'completed_sub_steps.sub_step_id')
                        ->where('completed_sub_steps.input_source_id', $masterInputSource->id)
                        ->where('building_id', $building->id);

                })
                ->orderByDesc('completed_sub_steps.created_at')
                ->first();

            // get all the completed steps
            $mostRecentCompletedStep = optional(
                $scan
                    ->completedSteps()
                    ->forInputSource($masterInputSource)
                    ->forBuilding($building)
                    ->orderByDesc('created_at')
                    ->first()
            )->step;

            // it could be that there is no completed step yet, in that case we just pick the first one.
            if (! $mostRecentCompletedStep instanceof Step) {
                $mostRecentCompletedStep = $scan->steps()->orderBy('order')->first();
            }
            if ($mostRecentCompletedSubStep instanceof SubStep) {
                $url = ScanFlowService::init($scan, $building, $masterInputSource)
                    ->forStep($mostRecentCompletedStep)
                    ->forSubStep($mostRecentCompletedSubStep)
                    ->resolveNextUrl();
            }

            // it could also be that there is no completed sub step, this will mean it's the user his first
            // time using the tool (yay)
            if (! $mostRecentCompletedSubStep instanceof SubStep) {
                $mostRecentCompletedSubStep = $mostRecentCompletedStep->subSteps()->orderBy('order')->first();

                $url = route('cooperation.frontend.tool.simple-scan.index', [
                    'scan' => $scan, 'step' => $mostRecentCompletedStep, 'subStep' => $mostRecentCompletedSubStep
                ]);
            }
        }
        return $url;
    }

    public function evaluateSubSteps(Collection $subSteps, ConditionEvaluator $evaluator): array
    {
        $building = $this->building;
        $currentInputSource = $this->currentInputSource;
        $masterInputSource = $this->inputSource;

        $subStepService = SubStepService::init()
            ->building($building)
            ->inputSource($currentInputSource);

        // The logic is as follows:
        // If a SubStep can be seen, and has all answers answered, we will complete it/keep it complete, and we will
        // check the Step because it might now be completable.
        // If a SubStep can be seen, but is missing some answers, we will incomplete it and/or the related Step.
        // If a SubStep cannot be seen, and it's complete, we incomplete it, and we will check the Step because it
        // might be now completable.

        $processedSubSteps = [];

        foreach ($subSteps as $subStep) {
            if ($evaluator->evaluate($subStep->conditions ?? [])) {
                // The SubStep is visible
                if ($this->hasAnsweredSubStep($subStep, $evaluator)) {
                    Log::debug("Completing SubStep {$subStep->name} because it has answers.");
                    $subStepService->subStep($subStep)->complete();
                    $stepsToCheck[] = $subStep->step->short;
                } else {
                    Log::debug("Incompleting SubStep {$subStep->name} and Step {$subStep->step->name} because SubStep is missing answers.");
                    $subStepService->subStep($subStep)->incomplete();
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
                    $subStepService->subStep($subStep)->incomplete();
                }

                // Add to array so we can check the Step completion later
                $stepsToCheck[] = $subStep->step->short;
            }

            $processedSubSteps[] = $subStep->id;
        }

        return $processedSubSteps;
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