<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('cooperation.admin.cooperation.cooperation-admin.settings.index');
    }
}
