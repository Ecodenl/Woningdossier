<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Models\Building;
use App\Models\Scan;
use App\Services\WoonplanService;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Sits on the woonplan while SmartTwin is still working on the advice, and moves the resident along
 * once there is something to show.
 *
 * Polling is the only way to notice: the results arrive on a webhook, in a request of their own.
 */
class AwaitingAdvice extends Component
{
    public Building $building;
    public Scan $scan;

    public function mount(Building $building, Scan $scan): void
    {
        $this->building = $building;
        $this->scan = $scan;
    }

    public function render(): View
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.awaiting-advice');
    }

    // Called from wire:poll
    public function checkForAdvice(): void
    {
        // Deliberately the same question the controller asks, rather than "has the callback gone".
        // GetAdviceResults clears the callback once the raw response is on disk and maps it into the
        // action plan after that, so between those two there is a moment where the callback is gone
        // and there is still nothing to show. Asking whether there is anything on the board means
        // this component and the controller cannot disagree about it.
        $hasAdvices = WoonplanService::init($this->building->refresh())
            ->scan($this->scan)
            ->hasAdvices();

        if ($hasAdvices) {
            $this->redirectRoute(
                'cooperation.frontend.tool.simple-scan.my-plan.index',
                ['scan' => $this->scan],
            );
        }
    }
}
