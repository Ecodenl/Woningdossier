<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\ExpertScan;

use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;
use Livewire\Component;

class Buttons extends Component
{
    public Scan $scan;
    public Step $step;

    public ?Questionnaire $questionnaire = null;

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.expert-scan.buttons');
    }
}