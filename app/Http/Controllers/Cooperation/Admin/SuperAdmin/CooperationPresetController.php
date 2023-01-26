<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationPreset;

class CooperationPresetController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $cooperationPresets = CooperationPreset::all();

        return view('cooperation.admin.super-admin.cooperation-presets.index', compact('cooperationPresets'));
    }

    public function show(Cooperation $cooperation, CooperationPreset $cooperationPreset)
    {
        return view('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperationPreset'));
    }
}
