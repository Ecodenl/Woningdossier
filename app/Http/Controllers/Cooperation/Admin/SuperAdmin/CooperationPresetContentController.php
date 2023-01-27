<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationPreset;
use App\Models\CooperationPresetContent;

class CooperationPresetContentController extends Controller
{
    public function create(Cooperation $cooperation, CooperationPreset $cooperationPreset)
    {
        return view('cooperation.admin.super-admin.cooperation-preset-contents.create', compact('cooperationPreset'));
    }

    public function edit(Cooperation $cooperation, CooperationPreset $cooperationPreset, CooperationPresetContent $cooperationPresetContent)
    {
        return view('cooperation.admin.super-admin.cooperation-preset-contents.edit', compact('cooperationPreset', 'cooperationPresetContent'));
    }

    public function destroy(Cooperation $cooperation, CooperationPreset $cooperationPreset, CooperationPresetContent $cooperationPresetContent)
    {
        $cooperationPresetContent->delete();

        return redirect()->route('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperation', 'cooperationPreset'))
            ->with('success', __('cooperation/admin/super-admin/cooperation-preset-contents.destroy.success'));
    }
}
