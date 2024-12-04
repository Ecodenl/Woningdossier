<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Http\RedirectResponse;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Helpers\Hoomdossier;

class AdminController extends Controller
{
    public function stopSession(Cooperation $cooperation): RedirectResponse
    {
        $user = \App\Helpers\Hoomdossier::user();
        $building = $user->building;
        $role = HoomdossierSession::getRole(true);

        $buildingId = HoomdossierSession::getBuilding(false);
        HoomdossierSession::switchRole($building, $role);

        // If they can start/stop a session, they can see a user's building.
        return redirect()->route('cooperation.admin.buildings.show', compact('building'));
    }

    public function index()
    {
        $user = Hoomdossier::user();

        // if the user only has one role we will redirect him to the right url
        if (1 == $user->getRoleNames()->count()) {
            $roleName = $user->getRoleNames()->first();

            return redirect()->route('cooperation.admin.switch-role', $roleName);
        }

        return view('cooperation.admin.choose-roles', compact('user'));
    }
}
