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
}
