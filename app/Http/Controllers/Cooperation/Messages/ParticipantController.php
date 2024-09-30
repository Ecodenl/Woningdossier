<?php

namespace App\Http\Controllers\Cooperation\Messages;

use Illuminate\Http\RedirectResponse;
use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Http\Controllers\Controller;
use App\Mail\User\NotifyCoachParticipantAdded;
use App\Mail\User\NotifyResidentParticipantAdded;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\PrivateMessageViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ParticipantController extends Controller
{
    /**
     * Remove a participant from a group chat and revoke his building access permissions.
     */
    public function revokeAccess(Cooperation $cooperation, Request $request): RedirectResponse
    {
        // get the group participant user id (which is only a coach, but still)
        $groupParticipantUserId = $request->get('user_id');
        // get the building owner id
        $buildingOwnerId = $request->get('building_owner_id');

        // the building from the user / resident
        $building = Building::find($buildingOwnerId);

        $revokedParticipant = User::find($groupParticipantUserId);

        if ($building instanceof Building) {
            // revoke the access for the coach to talk with the resident
            BuildingPermissionService::revokePermission($revokedParticipant, $building);
            BuildingCoachStatusService::revokeAccess($revokedParticipant, $building);

            ParticipantRevokedEvent::dispatch($revokedParticipant, $building);
        }

        return redirect()->back();
    }

    /**
     * Add a user / participant to a group chat and give him building access permission.
     */
    public function addWithBuildingAccess(Cooperation $cooperation, Request $request): RedirectResponse
    {
        $userId = $request->get('user_id', '');
        $buildingId = $request->get('building_id', '');

        // the receiver of the message
        $user = $cooperation->users()->find($userId);

        if ($user instanceof User) {
            $residentBuilding = Building::with('user')->find($buildingId);
            $resident = $residentBuilding->user;

            if ($resident->allowedAccess()) {
                // give the coach permission to the resident his building
                BuildingPermissionService::givePermission($user, $residentBuilding);
            }

            BuildingCoachStatusService::giveAccess($user, $residentBuilding);

            ParticipantAddedEvent::dispatch($user, $residentBuilding, $request->user(), $cooperation);

            $coachMail = (new NotifyCoachParticipantAdded($resident, $user))->onQueue(Queue::APP_EXTERNAL);
            $residentMail = (new NotifyResidentParticipantAdded($resident, $user))->onQueue(Queue::APP_EXTERNAL);

            Mail::to([['email' => $user->account->email, 'name'=> $user->getFullName()]])->queue($coachMail);
            Mail::to([['email' => $resident->account->email, 'name'=> $resident->getFullName()]])->queue($residentMail);
        }

        // since the coordinator is the only one who can do this atm.
        return redirect()->back()
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }

    /**
     * Method to set a collection of messages to read.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function setRead(Cooperation $cooperation, Request $request)
    {
        $isPublic = $request->get('is_public');
        $buildingId = $request->get('building_id');

        $messagesToSetRead = PrivateMessage::forMyCooperation()
            ->conversation($buildingId);

        // check which messages we have to set read
        if ($isPublic) {
            $messagesToSetRead = $messagesToSetRead->public();
        } else {
            $messagesToSetRead = $messagesToSetRead->private();
        }

        $messagesToSetRead = $messagesToSetRead->get();

        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            PrivateMessageViewService::markAsReadByCooperation($messagesToSetRead, $cooperation);
        } elseif (Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
            $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);
            PrivateMessageViewService::markAsReadByUser($messagesToSetRead, Hoomdossier::user(), $inputSource);
        } else {
            $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
            PrivateMessageViewService::markAsReadByUser($messagesToSetRead, Hoomdossier::user(), $inputSource);
        }
    }
}
