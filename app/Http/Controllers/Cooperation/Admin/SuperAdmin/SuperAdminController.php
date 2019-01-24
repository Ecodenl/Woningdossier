<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuperAdminController extends Controller
{
    public function index()
    {
        $cooperationCount = Cooperation::count();
        $userCount = User::count();
        $buildingCount = Building::count();

        return view('cooperation.admin.super-admin.index', compact('cooperationCount', 'userCount', 'buildingCount'));
    }
}
