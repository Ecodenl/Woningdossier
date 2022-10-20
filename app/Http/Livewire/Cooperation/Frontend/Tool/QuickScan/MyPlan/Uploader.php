<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Models\Building;
use Livewire\Component;
use Livewire\WithFileUploads;

class Uploader extends Component
{
    use WithFileUploads;

    public $building;
    public array $documents = [];

    public function mount(Building $building)
    {
        $this->building = $building;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.my-plan.uploader');
    }
}
