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
        // Normally we can let Livewire set the model for us, however, on create we don't have one. Yet, by casting
        // a model as null, we get a fresh model object, on which we can call things such as ->exists.
        $this->cooperationPresetContent = $cooperationPresetContent;
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
