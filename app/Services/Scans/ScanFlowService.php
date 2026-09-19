<?php

namespace App\Services\Scans;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\SmallMeasuresSettingHelper;
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
use App\Models\User;
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
     * Check if we should incomplete (sub) steps because conditional (sub) steps have come free, or if we need to
     * incomplete sub steps because they are hidden now.
     */
    public function checkConditionals(array $filledInAnswers, User $authUser)
    {
        $building = $this->building;
        $currentInputSource = $this->currentInputSource;
        // We must do it for the master also because we're not using model events
        $masterInputSource = $this->inputSource;

        $subStepService = SubStepService::init()
            ->building($building)
            ->inputSource($currentInputSource);

        $expertScan = Scan::expert();
        // Get all conditionally related sub steps that are not from expert steps.
        $subStepsRelated = SubStep::where(function ($query) use ($filledInAnswers) {
            $query->whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""]);
            foreach ($filledInAnswers as $toolQuestionShort => $answer) {
                $query->orWhereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestionShort}\""]);
            }
        })
            ->whereNotIn('id', $this->skipSubSteps)
            ->whereHas('step', fn ($q) => $q->where('scan_id', '!=', $expertScan->id))
            ->with('toolQuestions')
            ->get();

        // Get all conditionally related sub steppables that are not from expert steps.
        $subSteppableRelated = SubSteppable::where(function ($query) use ($filledInAnswers) {
            $query->whereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"fn\""]);
            foreach ($filledInAnswers as $toolQuestionShort => $answer) {
                $query->orWhereRaw('JSON_CONTAINS(conditions->"$**.column", ?, "$")', ["\"{$toolQuestionShort}\""]);
            }
        })
            ->where('sub_steppable_type', ToolQuestion::class)
            ->whereNotIn('sub_step_id', $this->skipSubSteps)
            ->whereNotIn('sub_step_id', $subStepsRelated->pluck('id')->toArray())
            ->whereHas('subStep', fn ($q) => $q->whereHas('step', fn ($q) => $q->where('scan_id', '!=', $expertScan->id)))
            ->with('subStep')
            ->get();

        $allConditions = $subStepsRelated->pluck('conditions')
            ->merge($subStepsRelated->pluck('toolQuestions.*.pivot.conditions')->flatten(1))
            ->merge($subSteppableRelated->pluck('conditions'))
            ->merge($subSteppableRelated->pluck('subStep.conditions'))
            ->filter()
            ->flatten(1)
            ->all();

        $evaluator = ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource);

        $evaluator->setAnswers($evaluator->getToolAnswersForConditions($allConditions));

        $stepsToCheck = [];
        $processedSubSteps = $this->evaluateSubSteps($subStepsRelated, $evaluator);

        // The logic is as follows:
        // We will simply check if the related SubStep has answers or not.

        // TODO: See if we can convert this to 'evaluateSubSteps' also
        foreach ($subSteppableRelated as $toolQuestionSubSteppable) {
            $subStep = $toolQuestionSubSteppable->subStep;

            // Skip if already processed
            if (! in_array($subStep->id, $processedSubSteps)) {
                if ($evaluator->evaluate($subStep->conditions ?? [])) {
                    if ($this->hasAnsweredSubStep($subStep, $evaluator)) {
                        $subStepService->subStep($subStep)->complete();
                        $stepsToCheck[] = $subStep->step->short;
                    } else {
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
                        $subStepService->subStep($subStep)->incomplete();
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
            $completed = StepHelper::completeStepIfNeeded($step, $building, $currentInputSource, $authUser);
            if (! $completed) {
                StepHelper::incomplete($step, $building, $currentInputSource);
            }
        }
    }

    private function canShow(SubStep $subStep): bool
    {
        return $this->building->user->account->can('show', [$subStep, $this->building]);
    }

    /**
     * The next sub step of this step the user may actually see.
     *
     * Picking the next one by order and letting the sub step conditions middleware bounce off
     * anything hidden costs one redirect per hidden sub step. That is tolerable for the occasional
     * conditional question and not tolerable when SmartTwin hides three whole steps at once.
     */
    private function nextShowableSubStep(Step $step, SubStep $after): ?SubStep
    {
        $subSteps = $step->subSteps()
            ->where('order', '>', $after->order)
            ->orderBy('order')
            ->get();

        /** @var SubStep $subStep */
        foreach ($subSteps as $subStep) {
            if ($this->canShow($subStep)) {
                return $subStep;
            }
        }

        return null;
    }

    /**
     * The first sub step the user may see, from this step onwards. Walks on to the step after when
     * a step has nothing showable in it, so a step that is hidden in its entirety is stepped over
     * rather than landed on and redirected away from.
     *
     * @return array{0: ?Step, 1: ?SubStep}
     */
    private function firstShowableSubStep(?Step $step): array
    {
        while ($step instanceof Step) {
            /** @var SubStep $subStep */
            foreach ($step->subSteps()->orderBy('order')->get() as $subStep) {
                if ($this->canShow($subStep)) {
                    return [$step, $subStep];
                }
            }

            $step = $step->nextStepForScan();
        }

        return [null, null];
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
            $nextSubStep = $this->nextShowableSubStep($this->step, $this->subStep);

            // Nothing left to show in this step, so we move on. This used to ask whether the current
            // sub step was the last one, which stops being the same question once the sub steps
            // after it can be hidden.
            if (! $nextSubStep instanceof SubStep) {
                // Let's check if there's questionnaires left
                if ($questionnaireService->hasActiveQuestionnaires()) {
                    $nextQuestionnaire = $questionnaireService
                        ->resolveQuestionnaire(true);
                } elseif ($this->scan->short !== Scan::EXPERT) {
                    // Unwanted behaviour for expert
                    [$nextStep, $nextSubStep] = $this->firstShowableSubStep($this->step->nextStepForScan());
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
                    [$nextStep, $nextSubStep] = $this->firstShowableSubStep($this->step->nextStepForScan());
                }
            }
        }

        if (! $nextStep instanceof Step) {
            // No next step set, let's see if there are any steps left incomplete
            $nextStep = $this->building->getFirstIncompleteStep($this->scan, $this->inputSource);
        }

        // There are incomplete steps left, set the sub step
        if (! $nextSubStep instanceof SubStep && $nextStep instanceof Step) {
            // retrieve all incomplete sub steps for the building
            $incompleteSubSteps = SubStepHelper::getIncompleteSubSteps(
                $this->building,
                $nextStep,
                $this->inputSource
            );
            foreach ($incompleteSubSteps as $subStep) {
                if ($this->building->user->account->can('show', [$subStep, $this->building])) {
                    $nextSubStep = $subStep;
                    break;
                }
            }
        }

        // Skip small-measures step if not enabled for this building
        if ($nextStep instanceof Step && $nextStep->short === 'small-measures') {
            if (! SmallMeasuresSettingHelper::isEnabledForBuilding($this->building, $this->scan)) {
                [$nextStep, $nextSubStep] = $this->firstShowableSubStep($nextStep->nextStepForScan());
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

            /** @var null|Step $mostRecentCompletedStep */
            $mostRecentCompletedStep = $scan->completedSteps()
                ->forInputSource($masterInputSource)
                ->forBuilding($building)
                ->orderByDesc('created_at')
                ->first()?->step;

            // it could be that there is no completed step yet, in that case we just pick the first one.
            if (! $mostRecentCompletedStep instanceof Step) {
                /** @var Step $mostRecentCompletedStep */
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
                // Not simply the first sub step of the first step: with SmartTwin enabled the first
                // steps are hidden in full, and landing on one of those would mean a redirect for
                // every sub step in them before the user sees a question.
                [$firstStep, $firstSubStep] = $this->firstShowableSubStep($mostRecentCompletedStep);

                if ($firstStep instanceof Step && $firstSubStep instanceof SubStep) {
                    $url = route('cooperation.frontend.tool.simple-scan.index', [
                        'scan' => $scan, 'step' => $firstStep, 'subStep' => $firstSubStep,
                    ]);
                } else {
                    // Nothing to ask at all; the plan is the only place left to go.
                    $url = route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'));
                }
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
                    $subStepService->subStep($subStep)->complete();
                    $stepsToCheck[] = $subStep->step->short;
                } else {
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
                    if (! empty($answer) || is_numeric($answer)) {
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
