<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

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

        $this->step = $step;
        $this->nextStep = $step->nextQuickScan();
        $this->previousStep = $step->previousQuickScan();

        $this->subStep = $subStep;
        $this->nextSubStep = $subStep->next();
        $this->previousSubStep = $subStep->previous();


        $this->toolQuestions = $subStep->toolQuestions;

        $this->total = Step::whereIn('short', ['building-data', 'usage-quick-scan', 'living-requirements', 'residential-status'])
            ->leftJoin('sub_steps', 'steps.id', '=', 'sub_steps.step_id')
            ->count();
        $this->current = $subStep->order;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan');
    }
}
