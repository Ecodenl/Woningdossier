<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index()
    {
        $buildingPermissions = BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->get();

        return view('cooperation.my-account.access.index', compact('buildingPermissions'));
    }

    public function revokeAccess(Cooperation $cooperation, Request $request)
    {
        // get the group participant user id which is only a coach, but still
        $groupParticipantUserId = $request->get('user_id');
        // get the building owner id
        $buildingOwnerId = $request->get('building_owner_id');

        // the building from the user / resident
        $building = Building::find($buildingOwnerId);

        if ($building instanceof Building) {
            // revoke the access for the coach to talk with the resident
            BuildingPermissionService::revokePermission($groupParticipantUserId, $building->id);
            BuildingCoachStatusService::revokeAccess($groupParticipantUserId, $building->id);

            // TODO: create a message ? to notify some admin ?
        }

        return redirect()->back();
    }
}
