<?php

namespace App\Livewire\Cooperation\Admin\Buildings;

use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use Livewire\Component;

class SmallMeasuresToggle extends Component
{
    public Building $building;
    public Cooperation $cooperation;
    public Scan $scan;
    public bool $enabled = false;
    public bool $cooperationEnabled;
    public bool $locked = false;

    public function mount(Building $building, Cooperation $cooperation, Scan $scan): void
    {
        $this->building = $building;
        $this->cooperation = $cooperation;
        $this->scan = $scan;
        $this->cooperationEnabled = SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, $scan);
        $this->enabled = SmallMeasuresSettingHelper::isEnabledForBuilding($building, $scan);
        // Lite scan always requires small measures
        $this->locked = $scan->isLiteScan();
    }

    public function updatedEnabled(bool $value): void
    {
        if ($this->locked) {
            $this->enabled = true;
            return;
        }

        SmallMeasuresSettingHelper::setOverride($this->building, $this->scan, $value);

        $this->dispatch(
            'alert-flash',
            type: 'success',
            message: __('cooperation/admin/buildings.small-measures.success')
        );
    }

    public function render()
    {
        return view('livewire.cooperation.admin.buildings.small-measures-toggle');
    }
}
