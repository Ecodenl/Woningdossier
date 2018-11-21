<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\AssignRoleRequest;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class AssignRoleController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users()->where('id', '!=', \Auth::id())->get();

        return view('cooperation.admin.cooperation.cooperation-admin.assign-role.index', compact('users'));
    }

    public function edit(Cooperation $cooperation, $userId)
    {
        // find the user by id, we will always start from the cooperation.
        $user = $cooperation->users()->findOrFail($userId);

        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->orWhere('name', 'coordinator')->get();

        return view('cooperation.admin.cooperation.cooperation-admin.assign-role.edit', compact('user', 'roles'));
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

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.assign-roles.index')->with('success', __('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.update.success'));
    }
}
