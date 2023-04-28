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
use Illuminate\Support\Facades\Log;

class SwitchRoleController extends Controller
{
    public function switchRole($cooperation, Request $request, $newRole)
    {
        // check if the targeted role exists
        if (! Role::where('name', $newRole)->exists()) {
            Log::debug("Role '$newRole' does not exist");

            return null;
        }

        $user = \App\Helpers\Hoomdossier::user();
        $building = $user->building;

        $role = Role::where('name', $newRole)->first();

        if (! $user || ! $user->hasRole($role)) {
            Log::debug('No user or user does not have this role');

            return redirect()->route('cooperation.admin.index');
        }

        $redirect = HoomdossierSession::switchRole($building, $role);

        if ($request->has('return')) {
            return redirect()->back();
        }
        
        return $redirect;
    }
}
