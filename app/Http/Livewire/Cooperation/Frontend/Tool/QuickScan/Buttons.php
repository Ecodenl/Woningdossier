<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Livewire\Component;

class Buttons extends Component
{
    private $account;

    public $step;
    public $previousStep;

    public $subStep;
    public $previousSubStep;

    public $questionnaire;
    public $previousQuestionnaire;

    public $previousUrl;

    public function mount(Request $request, Step $step, $subStepOrQuestionnaire)
    {
        $this->account = $request->user();

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->previousStep = $step;

        // We can either have a sub step or questionnaire. The previous and next buttons
        // will have to adapt to specific situations...
        if ($subStepOrQuestionnaire instanceof SubStep) {
            $subStep = $subStepOrQuestionnaire;

            $subStep->load(['toolQuestions', 'subStepTemplate']);

            // the route will always be matched, however a sub step has to match the step.
            abort_if(! $step->subSteps()->find($subStep->id) instanceof SubStep, 404);

            $this->subStep = $subStep;

        } elseif ($subStepOrQuestionnaire instanceof Questionnaire) {
            $questionnaire = $subStepOrQuestionnaire;

            abort_if($questionnaire->isNotActive() || $questionnaire->step->id !== $step->id, 404);

            $this->questionnaire = $questionnaire;
        } else {
            abort(404);
        }

        $this->setPreviousStep();
        $this->setUrl();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.buttons');
    }

    public function setUrl()
    {
        // TODO: See if we can integrate this with the ScanFlowService
        $previousStep = $this->previousStep;
        $previousSubStep = $this->previousSubStep;
        $previousQuestionnaire = $this->previousQuestionnaire;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $previousStep, 'subStep' => $previousSubStep]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['step' => $previousStep, 'questionnaire' => $previousQuestionnaire]);
        }

        $this->previousUrl = $previousUrl ?? '';
    }

    private function setPreviousStep()
    {
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
                    if ($this->previousStep->hasActiveQuestionnaires()) {
                        // There are questionnaires we need to look at
                        $this->previousQuestionnaire = $this->previousStep->questionnaires()->active()
                            ->orderByDesc('order')->first();
                    } else {
                        // the previous step is a different one, so we should get the last sub step of the previous step
                        $this->previousSubStep = $this->previousStep->subSteps()->orderByDesc('order')->first();
                    }
                }
            }

            if (isset($this->previousStep) && $this->account->cannot('show', $this->previousSubStep)) {
                // so the user is not allowed to see this sub step
                // now we also have to set the subStep so this won't do an infinite loop
                $this->subStep = $this->previousSubStep;
                $this->setPreviousStep();
            }
        } elseif ($this->questionnaire instanceof Questionnaire) {
            // We're currently in a questionnaire. We need to check if the previous button will be another questionnaire
            // or a quick scan step
            $potentialQuestionnaire = $this->step->questionnaires()->active()
                ->where('order', '<', $this->questionnaire->order)
                ->orderByDesc('order')->first();

            if ($potentialQuestionnaire instanceof Questionnaire) {
                $this->previousQuestionnaire = $potentialQuestionnaire;
            } else {
                // No more questionnaires, let's start the logic to get a previous sub step
                $this->previousSubStep = $this->step->subSteps()->orderByDesc('order')->first();

                if ($this->account->cannot('show', $this->previousSubStep)) {
                    // so the user is not allowed to see this sub step
                    // now we also have to set the subStep so this won't do an infinite loop
                    $this->subStep = $this->previousSubStep;
                    $this->setPreviousStep();
                }
            }
        }
    }
}
