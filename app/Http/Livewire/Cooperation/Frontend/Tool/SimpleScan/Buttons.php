<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan;

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

    public string $previousUrl;

    public function mount(Request $request, Scan $scan, Step $step, $subStepOrQuestionnaire)
    {
        $this->account = $request->user();
        $this->building = $this->account->user()->building;
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

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.buttons');
    }

    public function setUrl()
    {
        // TODO: See if we can integrate this with the ScanFlowService
        $scan = $this->scan;
        $previousStep = $this->previousStep;
        $previousSubStep = $this->previousSubStep;
        $previousQuestionnaire = $this->previousQuestionnaire;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.simple-scan.index', [
                'scan' => $scan,
                'step' => $previousStep,
                'subStep' => $previousSubStep
            ]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.simple-scan.questionnaires.index', [
                'scan' => $scan, 'step' => $previousStep, 'questionnaire' => $previousQuestionnaire
            ]);
        }

        $this->previousUrl = $previousUrl ?? '';
    }

    private function setPreviousStep()
    {
        $questionnaireService = QuestionnaireService::init()
            ->cooperation($this->cooperation)
            ->step($this->previousStep);

        if ($this->subStep instanceof SubStep) {
            $this->previousSubStep = $this
                ->step
                ->subSteps()
                ->where('order', '<', $this->subStep->order)
                ->orderByDesc('order')
                ->first();

            $firstSubStepForStep = $this->step->subSteps()->orderBy('order')->first();
            if ($firstSubStepForStep->id === $this->subStep->id) {
                $this->previousStep = $this->step->previousQuickScan();

                // the first one can't have a previous one
                if ($this->previousStep instanceof Step) {
                    if ($questionnaireService->hasActiveQuestionnaires()) {
                        // There are questionnaires we need to look at
                        $this->previousQuestionnaire = $questionnaireService->resolveQuestionnaire(false);
                    } else {
                        // the previous step is a different one, so we should get the last sub step of the previous step
                        $this->previousSubStep = $this->previousStep->subSteps()->orderByDesc('order')->first();
                    }
                }
            }

            if (isset($this->previousStep) && $this->account->cannot('show', [$this->previousSubStep, $this->building])) {
                // so the user is not allowed to see this sub step
                // now we also have to set the subStep so this won't do an infinite loop
                $this->subStep = $this->previousSubStep;
                $this->setPreviousStep();
            }
        } elseif ($this->questionnaire instanceof Questionnaire) {
            // We're currently in a questionnaire. We need to check if the previous button will be another questionnaire
            // or a quick scan step
            $potentialQuestionnaire = $questionnaireService
                ->questionnaire($this->questionnaire)
                ->resolveQuestionnaire(false);

            if ($potentialQuestionnaire instanceof Questionnaire) {
                $this->previousQuestionnaire = $potentialQuestionnaire;
            } else {
                // No more questionnaires, let's start the logic to get a previous sub step
                $this->previousSubStep = $this->step->subSteps()->orderByDesc('order')->first();

                if ($this->account->cannot('show', [$this->previousSubStep, $this->building])) {
                    // so the user is not allowed to see this sub step
                    // now we also have to set the subStep so this won't do an infinite loop
                    $this->subStep = $this->previousSubStep;
                    $this->setPreviousStep();
                }
            }
        }
    }
}
