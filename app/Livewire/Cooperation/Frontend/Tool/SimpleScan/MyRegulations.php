<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\MyRegulationHelper;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Models\NotificationService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\View\View;
use Livewire\Component;

class MyRegulations extends Component
{
    public array $relevantRegulations;
    public Building $building;
    public bool $isRefreshing;
    public InputSource $masterInputSource;

    public function mount(Building $building): void
    {
        $this->building = $building;
        $this->masterInputSource = InputSource::master();
        $this->relevantRegulations = MyRegulationHelper::getRelevantRegulations($building, $this->masterInputSource);
        $this->checkNotifications();
    }

    public function render(): View
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-regulations');
    }

    public function refreshRegulations(): void
    {
        UserActionPlanAdviceService::init()
            ->forUser($this->building->user)
            ->refreshUserRegulations();

        $this->isRefreshing = true;
    }

    public function checkNotifications(): void
    {
        $this->isRefreshing = NotificationService::init()
            ->forBuilding($this->building)
            ->setType(RefreshRegulationsForUserActionPlanAdvice::class)
            ->isActive();
    }

    public function checkIfIsRefreshed(): void
    {
        $oldIsRefreshing = $this->isRefreshing;
        $this->checkNotifications();

        if ($this->isRefreshing === false && $oldIsRefreshing !== $this->isRefreshing) {
            $this->relevantRegulations = MyRegulationHelper::getRelevantRegulations($this->building, $this->masterInputSource);
        }
    }
}
