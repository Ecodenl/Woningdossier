<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\RoleHelper;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation, $roleName = null)
    {
        // check if the $roleName is null or if the $roleName does not exists we redirect them to choose roles page
        if ($roleName == null || Role::where('name', $roleName)->count() == 0) {
            return redirect()->route('cooperation.admin.index');
        }

        $role = Role::findByName($roleName);
        session()->put('role_id', $role->id);

        return view('cooperation.admin.cooperation.coordinator.index');
    }
}
