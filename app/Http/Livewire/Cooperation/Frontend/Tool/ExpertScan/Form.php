<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Models\Step;
use Livewire\Component;

class Form extends Component
{
    public $step;

    public function mount(Step $step)
    {
        $this->step = $step;
    }
    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.form');
    }
}
