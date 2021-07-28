<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan;

use App\Models\Step;
use App\Models\SubStep;
use Livewire\Component;

class Form extends Component
{
    protected $listeners = ['save' => 'save'];

    public $step;
    public $subStep;

    public $toolQuestions;

    public function mount(Step $step, SubStep $subStep)
    {
        $subStep->load(['toolQuestions', 'subStepTemplate']);

        // set default steps, the checks will come later on.
        $this->step = $step;
        $this->subStep = $subStep;

        $this->toolQuestions = $subStep->toolQuestions;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.form');
    }

    public function save()
    {
        dd('save');
    }
}
