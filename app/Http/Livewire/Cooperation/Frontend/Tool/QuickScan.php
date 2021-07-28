<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

use App\Helpers\StepHelper;
use App\Models\Step;
use App\Models\SubStep;
use Livewire\Component;

class QuickScan extends Component
{

    public $step;
    public $nextStep;
    public $previousStep;

    public $subStep;
    public $nextSubStep;
    public $previousSubStep;

    public $toolQuestions;

    public int $current;
    public int $total;

    public function mount(Step $step, SubStep $subStep)
    {
        $subStep->load(['toolQuestions', 'subStepTemplate']);

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->previousStep = $step;
        $this->nextStep = $step;

        $this->subStep = $subStep;
        $this->nextSubStep = $subStep->next();
        $this->previousSubStep = $subStep->previous();

        // we will check if the current sub step is the last one, that way we know we have to go to the next one.
        $lastSubStepForStep = $step->subSteps()->orderByDesc('order')->first();
        $firstSubStepForStep = $step->subSteps()->orderBy('order')->first();


        if ($lastSubStepForStep->id === $subStep->id) {
            $this->nextStep = $step->nextQuickScan();
            // the previous step is a different one, so we should get the first sub step of the previous step
            $this->nextSubStep = $this->nextStep->subSteps()->first();
        }
        if ($firstSubStepForStep->id === $subStep->id) {
            $this->previousStep = $step->previousQuickScan();
            // the previous step is a different one, so we should get the last sub step of the previous step
            $this->previousSubStep = $this->previousSubStep->subSteps()->orderByDesc('order')->first();
        }


        $this->toolQuestions = $subStep->toolQuestions;

        $this->total = Step::whereIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS)
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->count();
        $this->current = $subStep->order;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan');
    }
}
