<?php

namespace App\Livewire\Cooperation\Admin\Buildings;

use App\Helpers\ScanAvailabilityHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use Livewire\Component;

class ScanAvailabilityToggle extends Component
{
    public Building $building;
    public Cooperation $cooperation;
    public Scan $scan;
    public bool $enabled = false;
    public bool $canToggle = true;
    public string $disabledReason = '';

    public function mount(Building $building, Cooperation $cooperation, Scan $scan): void
    {
        $this->building = $building;
        $this->cooperation = $cooperation;
        $this->scan = $scan;
        $this->enabled = ScanAvailabilityHelper::isAvailableForBuilding($building, $scan);

        $this->refreshToggleState();
    }

    public function updatedEnabled(bool $value): void
    {
        if ($value) {
            $result = ScanAvailabilityHelper::canEnable($this->building, $this->scan);
            if ($result !== true) {
                $this->enabled = false;
                $this->refreshToggleState();
                return;
            }
        } else {
            $result = ScanAvailabilityHelper::canDisable($this->building, $this->scan);
            if ($result !== true) {
                $this->enabled = true;
                $this->refreshToggleState();
                return;
            }
        }

        ScanAvailabilityHelper::setAvailability($this->building, $this->scan, $value);

        session()->flash('success', __('cooperation/admin/buildings.show.scan-availability.success'));

        $this->js('window.location.reload()');
    }

    private function refreshToggleState(): void
    {
        if ($this->enabled) {
            $result = ScanAvailabilityHelper::canDisable($this->building, $this->scan);
            $this->canToggle = $result === true;
            $this->disabledReason = $result === true ? '' : __($result);
        } else {
            $result = ScanAvailabilityHelper::canEnable($this->building, $this->scan);
            $this->canToggle = $result === true;
            $this->disabledReason = $result === true ? '' : __($result);
        }
    }

    public function render()
    {
        return view('livewire.cooperation.admin.buildings.scan-availability-toggle');
    }
}
