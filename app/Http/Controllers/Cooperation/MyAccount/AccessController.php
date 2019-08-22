<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserRevokedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;
use App\Services\BuildingCoachStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccessController extends Controller
{
    public function index()
    {
        $buildingPermissions = BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->get();

        /* @var Collection $conversationRequests */
        $conversationRequests = PrivateMessage::conversationRequestByBuildingId(HoomdossierSession::getBuilding())->get();

        return view('cooperation.my-account.access.index', compact('buildingPermissions', 'conversationRequests'));
    }

    public function allowAccess(Request $request)
    {
        $conversationRequests = PrivateMessage::conversationRequestByBuildingId(HoomdossierSession::getBuilding());
        if ($request->has('allow_access')) {
            $conversationRequests->update(['allow_access' => true]);
            $this->giveAccess();
        } else {
            $conversationRequests->update(['allow_access' => false]);
            $this->revokeAccess();
        }

        return redirect()->back();
    }

    /**
     * Method to give building access to all the connected coaches.
     */
    protected function giveAccess()
    {
        $coachesWithAccessToResidentBuildingStatuses = BuildingCoachStatus::getConnectedCoachesByBuildingId(HoomdossierSession::getBuilding());

        event(new UserAllowedAccessToHisBuilding());

        // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
        foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
            BuildingPermission::create([
                'user_id' => $coachWithAccessToResidentBuildingStatus->coach_id,
                'building_id' => $coachWithAccessToResidentBuildingStatus->building_id,
            ]);
        }
    }

    /**
     * Method to revoke the access for all the users connected to a building.
     *
     * @throws \Exception
     */
    protected function revokeAccess()
    {
        // get all the connected coaches to the building
        $connectedCoachesToBuilding = BuildingCoachStatus::getConnectedCoachesByBuildingId(HoomdossierSession::getBuilding());

        event(new UserRevokedAccessToHisBuilding());

        // and revoke them the access to the building
        foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
            BuildingCoachStatusService::revokeAccess($connectedCoachToBuilding->coach_id, $connectedCoachToBuilding->building_id);
        }

        // delete all the building permissions for this building
        BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->delete();
    }
}
