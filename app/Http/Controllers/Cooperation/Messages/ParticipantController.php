<?php

namespace App\Http\Controllers\Cooperation\Messages;

use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Remove a participant from a group chat and revoke his building access permissions.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
            $revokedParticipant = User::find($groupParticipantUserId);

            event(new ParticipantRevokedEvent($revokedParticipant, $building));
        }

        return redirect()->back();
    }

    /**
     * Add a user / participant to a group chat and give him building access permission.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addWithBuildingAccess(Cooperation $cooperation, Request $request)
    {
        $userId = $request->get('user_id', '');
        $buildingId = $request->get('building_id', '');

        // the receiver of the message
        $user = $cooperation->users()->find($userId);

        if ($user instanceof User) {
            $residentBuilding = Building::find($buildingId);

            $privateMessage = PrivateMessage::forMyCooperation()->conversationRequest($buildingId)->first();

            if ($privateMessage->allow_access) {
                // give the coach permission to the resident his building
                BuildingPermissionService::givePermission($userId, $buildingId);
            }

            BuildingCoachStatusService::giveAccess($userId, $buildingId);

            event(new ParticipantAddedEvent($user, $residentBuilding));
        }

        // since the coordinator is the only one who can do this atm.
        return redirect()->back()
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }
}
