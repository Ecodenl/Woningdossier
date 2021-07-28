<?php

namespace App\Http\Livewire\Cooperation\Tool;

use App\Models\Step;
use App\Models\SubStep;
use Livewire\Component;

class QuickScan extends Component
{
    public $step;
    public $subStep;

    public function mount(Step $step, SubStep $subStep)
    {
        $this->step = $step;
        $this->subStep = $subStep;
    }

    public function render()
    {
        return view('livewire.cooperation.tool.quick-scan');
    }
}
