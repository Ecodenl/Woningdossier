<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationPreset;

class CooperationPresetController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $cooperationPresets = CooperationPreset::all();

        return view('cooperation.admin.super-admin.cooperation-presets.index', compact('cooperationPresets'));
    }

    public function show(Cooperation $cooperation, CooperationPreset $cooperationPreset): View
    {
        return view('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperationPreset'));
    }
}
