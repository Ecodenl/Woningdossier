<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Http\RedirectResponse;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function assignRole(Cooperation $cooperation, Request $request): RedirectResponse
    {
        // the user id to assign the role to
        $userId = $request->get('user_id');
        // the role we want to assign to a user
        $roleId = $request->get('role_id');

        $role = Role::findById($roleId);

        // if the user is a super-admin, then hey may be assigning roles to user from a other cooperation, so we remove the scope.
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin')) {
            $user = User::withoutGlobalScopes()->find($userId);
        } else {
            $user = User::find($userId);
        }

        $this->authorize('view', [$role, Hoomdossier::user(), \App\Helpers\HoomdossierSession::getRole(true)]);


        $user->assignRole($role);

        return redirect()->back();
    }

    public function removeRole(Cooperation $cooperation, Request $request): RedirectResponse
    {
        $currentUser = Hoomdossier::user();
        // the user id to assign the role to
        $userId = $request->get('user_id');
        // the role we want to remove from the a user
        $roleId = $request->get('role_id');
        $role = Role::findById($roleId);

        // if the user is a super-admin, then hey may be removing roles from a user from a other cooperation, so we remove the scope.
        if ($currentUser->hasRoleAndIsCurrentRole('super-admin')) {
            $user = User::forAllCooperations()->find($userId);
        } else {
            $user = User::find($userId);
        }

        $this->authorize('delete', [$role, Hoomdossier::user(), \App\Helpers\HoomdossierSession::getRole(true), $user]);

        // we cant delete a role if the user only has 1 role.
        if ($user->hasMultipleRoles()) {
            $user->removeRole($role);
        }

        return redirect()->back();
    }
}
