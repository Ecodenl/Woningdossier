<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Models\Scan;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Sits on the woonplan while SmartTwin is still working on the advice, and moves the resident along
 * once it is in.
 *
 * The callback for a flow is removed when its results have been processed, so its absence is the
 * signal that there is something to show. Polling is the only way to notice: the results arrive on
 * a webhook, in another request entirely.
 */
class AwaitingAdvice extends Component
{
    public Building $building;
    public Scan $scan;

    /**
     * The enum's value rather than the enum. Livewire round-trips its properties to the browser,
     * and assigns what it is handed straight onto the typed property before mount() gets a look in.
     */
    public string $eventType;

    public function mount(Building $building, Scan $scan, string $eventType): void
    {
        $this->building = $building;
        $this->scan = $scan;
        $this->eventType = $eventType;
    }

    public function render(): View
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.awaiting-advice');
    }

    // Called from wire:poll
    public function checkForAdvice(): void
    {
        $eventType = EventType::tryFrom($this->eventType);

        if (! $eventType instanceof EventType) {
            return;
        }

        if (! $this->building->refresh()->hasSmartTwinCallback($eventType)) {
            $this->redirectRoute(
                'cooperation.frontend.tool.simple-scan.my-plan.index',
                ['scan' => $this->scan],
            );
        }
    }
}
