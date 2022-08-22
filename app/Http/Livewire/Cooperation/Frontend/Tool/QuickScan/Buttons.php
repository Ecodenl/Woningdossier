<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Livewire\Component;

class Buttons extends Component
{
    private $account;
    /** @var Building */
    public $building;
    public $masterInputSource;

    public $step;
    public $nextStep;
    public $previousStep;

    public $currentScan;

    public $subStep;
    public $nextSubStep;
    public $previousSubStep;

    public $questionnaire;
    public $nextQuestionnaire;
    public $previousQuestionnaire;

    public $firstIncompleteStep = null;
    public $firstIncompleteSubStep = null;

    public $toolQuestions;

    public $nextUrl;
    public $previousUrl;

    public function mount(Request $request, Scan $currentScan, Step $step, $subStepOrQuestionnaire)
    {
        $this->currentScan = $currentScan;
        $this->account = $request->user();
        $this->building = HoomdossierSession::getBuilding(true);

        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->previousStep = $step;
        $this->nextStep = $step;

        // We can either have a sub step or questionnaire. The previous and next buttons
        // will have to adapt to specific situations...
        if ($subStepOrQuestionnaire instanceof SubStep) {
            $subStep = $subStepOrQuestionnaire;

            $subStep->load(['toolQuestions', 'subStepTemplate']);

            // the route will always be matched, however a sub step has to match the step.
            abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);

            $this->subStep = $subStep;

        } elseif ($subStepOrQuestionnaire instanceof Questionnaire) {
            $questionnaire = $subStepOrQuestionnaire;

            abort_if($questionnaire->isNotActive() || $questionnaire->step->id !== $step->id, 404);

            $this->questionnaire = $questionnaire;
        } else {
            abort(404);
        }

        $this->setNextStep();
        $this->setPreviousStep();
        $this->setUrl();


    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.buttons');
    }

    public function setUrl()
    {
        $firstIncompleteStep = $this->firstIncompleteStep;
        $firstIncompleteSubStep = $this->firstIncompleteSubStep;
        $previousStep = $this->previousStep;
        $previousSubStep = $this->previousSubStep;
        $previousQuestionnaire = $this->previousQuestionnaire;
        $nextQuestionnaire = $this->nextQuestionnaire;
        $nextSubStep = $this->nextSubStep;
        $currentScan = $this->currentScan;
        $nextStep = $this->nextStep;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $previousStep->scan, 'step' => $previousStep, 'subStep' => $previousSubStep]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['scan' => $previousStep->scan, 'step' => $previousStep, 'questionnaire' => $previousQuestionnaire]);
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

    private function setNextStep()
    {
        if ($this->subStep instanceof SubStep) {
            $this->nextSubStep = $this->step->subSteps()->where('order', '>', $this->subStep->order)->orderBy('order')->first();
            // we will check if the current sub step is the last one, that way we know we have to go to the next one.
            $lastSubStepForStep = $this->step->subSteps()->orderByDesc('order')->first();
            if ($lastSubStepForStep->id === $this->subStep->id) {
                // Let's check if there's questionnaires left
                if ($this->step->hasActiveQuestionnaires()) {
                    $this->nextQuestionnaire = $this->step->questionnaires()->active()->orderBy('order')->first();
                } else {
                    $this->nextStep = $this->step->nextQuickScan();
                    // the last can't have a next one
                    if ($this->nextStep instanceof Step) {
                        // the previous step is a different one, so we should get the first sub step of the previous step
                        $this->nextSubStep = $this->nextStep->subSteps()->orderBy('order')->first();
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
                $this->nextStep = $this->step->nextQuickScan();
                // the last can't have a next one
                if ($this->nextStep instanceof Step) {
                    // the previous step is a different one, so we should get the first sub step of the previous step
                    $this->nextSubStep = $this->nextStep->subSteps()->orderBy('order')->first();
                }
            }
        }

        if (! $this->nextStep instanceof Step) {
            // No next step set, let's see if there are any steps left incomplete
            $this->firstIncompleteStep = $this->building->getFirstIncompleteStep([$this->step->id], $this->masterInputSource);
        }

        // There are incomplete steps left, set the sub step
        if ($this->firstIncompleteStep instanceof Step) {
            $this->firstIncompleteSubStep = $this->building->getFirstIncompleteSubStep($this->firstIncompleteStep, [], $this->masterInputSource);
        }
    }
}
