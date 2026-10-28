<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\Models\QuestionnaireService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Livewire\Component;

class Buttons extends Component
{
    private Account $account;
    private Building $building;
    private Cooperation $cooperation;

    public Scan $scan;
    public Step $step;
    public ?Step $previousStep = null;

    public ?SubStep $subStep = null;
    public ?SubStep $previousSubStep = null;

    public ?Questionnaire $questionnaire = null;
    public ?Questionnaire $previousQuestionnaire = null;

    public string $previousUrl = '';

    public function mount(Request $request, Scan $scan, Step $step, $subStepOrQuestionnaire): void
    {
        $this->account = $request->user();
        $this->building = HoomdossierSession::getBuilding(true);
        $this->cooperation = HoomdossierSession::getCooperation(true);

        // set default steps, the checks will come later on.
        $this->previousStep = $step;

        // We can either have a sub step or questionnaire. The previous and next buttons
        // will have to adapt to specific situations...
        if ($subStepOrQuestionnaire instanceof SubStep) {
            $subStepOrQuestionnaire->load(['toolQuestions', 'subStepTemplate']);
            $this->subStep = $subStepOrQuestionnaire;
        } elseif ($subStepOrQuestionnaire instanceof Questionnaire) {
            $subStepOrQuestionnaire->load(['questions', 'questions.questionOptions', 'steps']);
            $this->questionnaire = $subStepOrQuestionnaire;
        } else {
            abort(404);
        }

        $this->setPreviousStep();
        $this->setUrl();
    }

    public function render(): View
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.buttons');
    }

    // Private, so it cannot be called as a Livewire action: those requests skip the cooperation
    // middleware, and the cooperation property is only set in mount().
    private function setUrl(): void
    {
        // TODO: See if we can integrate this with the ScanFlowService
        $scan = $this->scan;
        $previousStep = $this->previousStep;
        $previousSubStep = $this->previousSubStep;
        $previousQuestionnaire = $this->previousQuestionnaire;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.simple-scan.index', [
                'cooperation' => $this->cooperation,
                'scan' => $scan,
                'step' => $previousStep,
                'subStep' => $previousSubStep
            ]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.simple-scan.questionnaires.index', [
                'cooperation' => $this->cooperation, 'scan' => $scan, 'step' => $previousStep, 'questionnaire' => $previousQuestionnaire
            ]);
        }

        $this->previousUrl = $previousUrl ?? '';
    }

    private function setPreviousStep(): void
    {
        if ($this->subStep instanceof SubStep) {
            $this->previousSubStep = $this->lastShowableSubStep($this->step, $this->subStep);
        } elseif ($this->questionnaire instanceof Questionnaire) {
            // We're currently in a questionnaire. We need to check if the previous button will be another questionnaire
            // or a quick scan step
            $this->previousQuestionnaire = QuestionnaireService::init()
                ->cooperation($this->cooperation)
                ->step($this->step)
                ->questionnaire($this->questionnaire)
                ->resolveQuestionnaire(false);

            if (! $this->previousQuestionnaire instanceof Questionnaire) {
                // The questionnaires come after the sub steps, so the last one we may see is next in line.
                $this->previousSubStep = $this->lastShowableSubStep($this->step);
            }
        }

        if (! $this->previousSubStep instanceof SubStep && ! $this->previousQuestionnaire instanceof Questionnaire) {
            $this->setPreviousFromStepsBefore($this->step);
        }
    }

    /**
     * Where the user ends up when walking back out of the given step: the questionnaires of the step
     * before, or else its last sub step they may see. This used to take the last sub step and bounce
     * off it while hidden, which only looked within the current step; a step that is hidden in its
     * entirety, as SmartTwin does, then sent the user to a sub step under a step it isn't part of.
     * A step without any sub step to show is stepped over, questionnaires and all, like the next
     * button does.
     */
    private function setPreviousFromStepsBefore(Step $step): void
    {
        $questionnaireService = QuestionnaireService::init()->cooperation($this->cooperation);

        $this->previousStep = $step->previousStepForScan();
        while ($this->previousStep instanceof Step) {
            $previousSubStep = $this->lastShowableSubStep($this->previousStep);

            if ($previousSubStep instanceof SubStep) {
                if ($questionnaireService->step($this->previousStep)->hasActiveQuestionnaires()) {
                    $this->previousQuestionnaire = $questionnaireService->resolveQuestionnaire(false);
                } else {
                    $this->previousSubStep = $previousSubStep;
                }

                return;
            }

            $this->previousStep = $this->previousStep->previousStepForScan();
        }
    }

    /** The last sub step of the given step the user may see, before the given sub step if passed. */
    private function lastShowableSubStep(Step $step, ?SubStep $before = null): ?SubStep
    {
        $subSteps = $step->subSteps()
            ->when($before instanceof SubStep, fn ($query) => $query->where('order', '<', $before->order))
            ->orderByDesc('order')
            ->get();

        /** @var SubStep $subStep */
        foreach ($subSteps as $subStep) {
            if ($this->account->can('show', [$subStep, $this->building])) {
                return $subStep;
            }
        }

        return null;
    }
}
