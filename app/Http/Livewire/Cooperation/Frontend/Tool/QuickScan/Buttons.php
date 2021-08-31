<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Livewire\Component;

class Buttons extends Component
{
    private $account;
    /** @var Building */
    public $building;
    public $step;
    public $nextStep;
    public $previousStep;

    public $subStep;
    public $nextSubStep;
    public $previousSubStep;

    public $firstIncompleteStep = null;
    public $firstIncompleteSubStep = null;

    public $toolQuestions;

    public function mount(Request $request, Step $step, SubStep $subStep)
    {
        $this->account = $request->user();
        $this->building = HoomdossierSession::getBuilding(true);

        $subStep->load(['toolQuestions', 'subStepTemplate']);

        // the route will always be matched, however a sub step has to match the step.
        abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->previousStep = $step;
        $this->nextStep = $step;

        $this->subStep = $subStep;


        $this->setNextStep();
        $this->setPreviousStep();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.buttons');
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
                    // the previous step is a different one, so we should get the last sub step of the previous step
                    $this->previousSubStep = $this->previousStep->subSteps()->orderByDesc('order')->first();
                }
            }

            if ($this->account->cannot('show', $this->previousSubStep)) {
                // so the user is not allowed to see this sub step
                // now we also have to set the subStep so this wont do a infinite loop
                $this->subStep = $this->previousSubStep;
                $this->setPreviousStep();
            }
        }
    }

    private function setNextStep()
    {
        $this->nextSubStep = $this->step->subSteps()->where('order', '>', $this->subStep->order)->orderBy('order')->first();
        // we will check if the current sub step is the last one, that way we know we have to go to the next one.
        $lastSubStepForStep = $this->step->subSteps()->orderByDesc('order')->first();
        if ($lastSubStepForStep->id === $this->subStep->id) {
            $this->nextStep = $this->step->nextQuickScan();
            // the last can't have a next one
            if ($this->nextStep instanceof Step) {
                // the previous step is a different one, so we should get the first sub step of the previous step
                $this->nextSubStep = $this->nextStep->subSteps()->first();
            }
        }

        if (! $this->nextStep instanceof Step) {
            // No next step set, let's see if there are any steps left incomplete

            $irrelevantSteps = $this->building->completedSteps()->pluck('step_id')->toArray();
            $irrelevantSteps[] = $this->step->id;
            $this->firstIncompleteStep = Step::quickScan()
                ->whereNotIn('id', $irrelevantSteps)
                ->orderBy('order')
                ->first();
        }

        // There are incomplete steps left, set the sub step
        if ($this->firstIncompleteStep instanceof Step) {
            $this->firstIncompleteSubStep = $this->firstIncompleteStep->subSteps()->orderBy('order')->first();
        }
    }
}
