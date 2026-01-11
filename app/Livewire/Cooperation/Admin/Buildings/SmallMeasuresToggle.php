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

    public function mount(Building $building, Cooperation $cooperation, Scan $scan): void
    {
        $this->building = $building;
        $this->cooperation = $cooperation;
        $this->scan = $scan;
        $this->cooperationEnabled = SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, $scan);
        $this->enabled = SmallMeasuresSettingHelper::hasOverride($building, $scan);
    }

    public function updatedEnabled(bool $value): void
    {
        // Only allow when cooperation setting is OFF
        if ($this->cooperationEnabled) {
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
