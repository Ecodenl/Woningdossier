<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CoachController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $buildingPermissions = \Auth::user()->buildingPermissions;

        return view('cooperation.admin.coach.index', compact('buildingPermissions'));
    }
}
