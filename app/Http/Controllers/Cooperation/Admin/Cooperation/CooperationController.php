<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class CooperationController extends Controller
{
    public function index(Cooperation $cooperation, $roleName)
    {
        $role = Role::findByName($roleName);
        session()->put('role_id', $role->id);

        return view('cooperation.admin.cooperation.cooperation-admin.index');
    }
}
