<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Http\Requests\Admin\Cooperation\Coordinator\AssignRoleRequest;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class AssignRoleController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users;

        return view('cooperation.admin.cooperation.coordinator.assign-role.index', compact('users'));
    }

    public function edit(Cooperation $cooperation, $userId)
    {

        // find the user by id, we will always start from the cooperation.
        $user = $cooperation->users()->findOrFail($userId);

        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->get();

        return view('cooperation.admin.cooperation.coordinator.assign-role.edit', compact('user', 'roles'));
    }

    public function update(Cooperation $cooperation, $userId, AssignRoleRequest $request)
    {

        $user = $cooperation->users()->findOrFail($userId);

        $roleIds = $request->get('roles', '');
        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            array_push($roles, $role->name);
        }

        // assign the roles to the user
        $user->syncRoles();
        $user->assignRole($roles);

        return redirect()->route('cooperation.admin.cooperation.coordinator.assign-roles.index')->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.update.success'));
    }
}
