<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CoachController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $buildingPermissions = \Auth::user()->buildingPermissions;

        return view('cooperation.admin.coach.index', compact('buildingPermissions'));
    }
}
