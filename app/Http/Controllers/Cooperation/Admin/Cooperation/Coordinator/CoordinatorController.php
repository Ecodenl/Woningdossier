<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\RoleHelper;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users;

        return view('cooperation.admin.cooperation.coordinator.index', compact('users'));
    }
}
