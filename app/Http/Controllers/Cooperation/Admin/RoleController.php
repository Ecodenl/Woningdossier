<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function assignRole(Cooperation $cooperation, Request $request)
    {
        // the user id to assign the role to
        $userId = $request->get('user_id');
        // the role we want to assign to a user
        $roleId = $request->get('role_id');

        $cooperationId = $request->get('cooperation_id', null);

        $role = Role::findById($roleId);
        $user = User::find($userId);

        $user->assignRole($role);

        return redirect()->back();
    }

    public function removeRole(Cooperation $cooperation, Request $request)
    {
        // the user id to assign the role to
        $userId = $request->get('user_id');
        // the role we want to assign to a user
        $roleId = $request->get('role_id');

        $role = Role::findById($roleId);
        $user = User::find($userId);

        // we cant delete a role if the user only has 1 role.
        if ($user->hasMultipleRoles()) {
            $user->removeRole($role);
        }

        return redirect()->back();
    }
}
