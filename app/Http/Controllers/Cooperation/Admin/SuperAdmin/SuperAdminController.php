<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index(): View
    {
        $cooperationCount = Cooperation::count();
        $userCount = User::withoutGlobalScopes()->count();
        $buildingCount = Building::count();

        return view('cooperation.admin.super-admin.index', compact('cooperationCount', 'userCount', 'buildingCount'));
    }
}
