<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class SwitchRoleController extends Controller
{
    public function switchRole($cooperation, Request $request, $newRole)
    {
        // check if the targeted role exists
        if (! Role::where('name', $newRole)->exists()) {
            \Log::debug("Role '$newRole' does not exist");

            return null;
        }
        /** @var User $user */
        $user = $request->user();
        /** @var Building $building */
        $building = $user->buildings()->first();

        $role = Role::where('name', $newRole)->first();

        if (! $user || ! $user->hasRole($role)) {
            \Log::debug('No user or user does not have this role');

            return null;
        }

        \Log::debug('Switching roles from '.$request->session()->get('role_id', '').' to '.$role->id);
        $request->session()->put('role_id', $role->id);

        // set the Auth user sessions
        HoomdossierSession::setRole($role);
        if ($role->inputSource instanceof InputSource) {
            HoomdossierSession::setInputSource($role->inputSource);
            // For now, we will set the input source value to input source.
            // The user can later on change this.
            HoomdossierSession::setInputSourceValue($role->inputSource);
        }

        HoomdossierSession::setBuilding($building);
        HoomdossierSession::setIsObserving(false);


        if ($request->has('return')) {
            return redirect()->back();
        }

        return redirect(RoleHelper::getUrlByRole($role));
    }
}
