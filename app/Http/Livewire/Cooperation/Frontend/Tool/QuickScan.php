<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool;

use App\Models\Step;
use App\Models\SubStep;
use Livewire\Component;

class QuickScan extends Component
{
    public $step;
    public $subStep;

    public $toolQuestions;

    public function mount(Step $step, SubStep $subStep)
    {
        $this->step = $step;
        $this->subStep = $subStep;

        $this->toolQuestions = $subStep->toolQuestions();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan');
    }
}
