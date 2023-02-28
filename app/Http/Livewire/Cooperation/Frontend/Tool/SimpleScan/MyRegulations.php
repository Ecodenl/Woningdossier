<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\MyRegulationHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\UserActionPlanAdviceService;
use Livewire\Component;

class MyRegulations extends Component
{
    public array $relevantRegulations;
    public Building $building;
    public bool $isRefreshing;

    public function mount(Building $building)
    {
        $this->building = $building;
        $masterInputSource = InputSource::master();
        $this->relevantRegulations = MyRegulationHelper::getRelevantRegulations($building, $masterInputSource);
        $this->isRefreshing = $building->user->refreshing_regulations;
    }
    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-regulations');
    }

    public function refreshRegulations()
    {
        $this->building->user->update(['refreshing_regulations' => true]);

        UserActionPlanAdviceService::init()
            ->forUser($this->building->user)
            ->refreshUserRegulations();
    }

    public function checkIfIsRefreshed()
    {
        $this->isRefreshing = $this->building->user->refreshing_regulations;
    }
}
