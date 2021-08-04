<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\StepHelper;
use App\Models\Step;
use App\Models\SubStep;
use Livewire\Component;

class Buttons extends Component
{
    public $step;
    public $nextStep;
    public $previousStep;

    public $subStep;
    public $nextSubStep;
    public $previousSubStep;

    public $toolQuestions;

    public function mount(Step $step, SubStep $subStep)
    {
        $subStep->load(['toolQuestions', 'subStepTemplate']);

        // the route will always be matched, however a sub step has to match the step.
        abort_if(!$step->subSteps()->find($subStep->id) instanceof SubStep, 404);

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->previousStep = $step;
        $this->nextStep = $step;

        $this->subStep = $subStep;


        $this->nextSubStep = $this->step->subSteps()->where('order', '>', $this->subStep->order)->orderBy('order')->first();
        // we will check if the current sub step is the last one, that way we know we have to go to the next one.
        $lastSubStepForStep = $step->subSteps()->orderByDesc('order')->first();
        if ($lastSubStepForStep->id === $this->subStep->id) {
            $this->nextStep = $step->nextQuickScan();
            // the last cant have a next one
            if ($this->nextStep instanceof Step) {
                // the previous step is a different one, so we should get the first sub step of the previous step
                $this->nextSubStep = $this->nextStep->subSteps()->first();
            }
        }

        $this->previousSubStep = $this->step->subSteps()->where('order', '<', $this->subStep->order)->orderByDesc('order')->first();
        $firstSubStepForStep = $step->subSteps()->orderBy('order')->first();
        if ($firstSubStepForStep->id === $subStep->id) {
            $this->previousStep = $step->previousQuickScan();

            // the first one cant have a previous one
            if ($this->previousStep instanceof Step) {
                // the previous step is a different one, so we should get the last sub step of the previous step
                $this->previousSubStep = $this->previousStep->subSteps()->orderByDesc('order')->first();
            }
        }
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.buttons');
    }
}
