<?php

namespace App\Http\Livewire\Cooperation\Admin\SuperAdmin\CooperationPresets\CooperationPresetContents\CooperationMeasureApplications;

use App\Models\CooperationPreset;
use App\Models\CooperationPresetContent;
use Livewire\Component;

class Form extends Component
{
    public CooperationPreset $cooperationPreset;
    public ?CooperationPresetContent $cooperationPresetContent;

    public array $content = [];

    public function mount(?CooperationPresetContent $cooperationPresetContent = null)
    {
        if ($cooperationPresetContent->exists) {
            $this->fill([
                'content' => $cooperationPresetContent->content,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.cooperation-measure-applications.form');
    }
}
