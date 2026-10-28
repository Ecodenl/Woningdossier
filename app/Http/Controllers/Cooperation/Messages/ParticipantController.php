<?php

namespace App\Http\Controllers\Cooperation\Messages;

use App\Models\PrivateMessageView;
use Illuminate\Http\RedirectResponse;
use App\Events\ParticipantAddedEvent;
use App\Events\ParticipantRevokedEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Helpers\RoleHelper;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class ParticipantController extends Controller
{
    /**
     * Remove a participant from a group chat and revoke his building access permissions.
     */
    public function revokeAccess(Cooperation $cooperation, Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'building_owner_id' => 'required|integer',
        ]);

        // the building from the user / resident, and the coach to detach from it
        $building = Building::findOrFail($request->input('building_owner_id'));
        $revokedParticipant = User::findOrFail($request->input('user_id'));

        Gate::authorize('revoke-coach-access', [$building, $revokedParticipant]);

        // revoke the access for the coach to talk with the resident
        BuildingPermissionService::revokePermission($revokedParticipant, $building);
        BuildingCoachStatusService::revokeAccess($revokedParticipant, $building);

        ParticipantRevokedEvent::dispatch($revokedParticipant, $building);

        return redirect()->back();
    }

    /**
     * Add a user / participant to a group chat and give him building access permission.
     */
    public function addWithBuildingAccess(Cooperation $cooperation, Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'building_id' => 'required|integer',
        ]);

        $residentBuilding = Building::with('user')->findOrFail($request->input('building_id'));

        // Explicitly, as the cooperation scope on users is left off in the console (and so in tests).
        $resident = $residentBuilding->user;
        abort_unless($resident instanceof User && $resident->cooperation_id == $cooperation->id, 404);

        Gate::authorize('edit', $residentBuilding);

        // the receiver of the message
        $user = $cooperation->users()->find($request->input('user_id'));
        abort_unless($user instanceof User && $user->hasRole(RoleHelper::ROLE_COACH), 404);

        // Attaching an attached coach again would take two revokes to detach them.
        $isAttached = BuildingCoachStatusService::getConnectedCoachesByBuilding($residentBuilding)
            ->contains('coach_id', $user->id);

        if (! $isAttached) {
            if ($resident->allowedAccess()) {
                // give the coach permission to the resident his building
                BuildingPermissionService::givePermission($user, $residentBuilding);
            }

            BuildingCoachStatusService::giveAccess($user, $residentBuilding);

            ParticipantAddedEvent::dispatch($user, $residentBuilding, $request->user(), $cooperation);

            $coachMail = (new NotifyCoachParticipantAdded($resident, $user))->onQueue(Queue::APP_EXTERNAL);
            $residentMail = (new NotifyResidentParticipantAdded($resident, $user))->onQueue(Queue::APP_EXTERNAL);

            Mail::to([['email' => $user->account->email, 'name' => $user->getFullName()]])->queue($coachMail);
            Mail::to([['email' => $resident->account->email, 'name' => $resident->getFullName()]])->queue($residentMail);
        }

        // since the coordinator is the only one who can do this atm.
        return redirect()->back()
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.store.success'));
    }

    /**
     * Method to set a collection of messages to read.
     */
    public function setRead(Cooperation $cooperation, Request $request)
    {
        $request->validate([
            'building_id' => 'required|integer',
        ]);

        $isPublic = (bool) $request->input('is_public');
        $building = Building::findOrFail($request->input('building_id'));
        $buildingId = $building->id;

        // Called from the building overview in the admin, so whoever may see that may mark it read.
        Gate::authorize('show', $building);

        $messagesToSetRead = PrivateMessage::forMyCooperation()
            ->conversation($buildingId);

        // check which messages we have to set read
        if ($isPublic) {
            $messagesToSetRead = $messagesToSetRead->public();
        } else {
            $messagesToSetRead = $messagesToSetRead->private();
        }

        $messagesToSetRead = $messagesToSetRead->get();

        // The gate above lets only these through: the cooperation's staff, or a coach attached to the building.
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            PrivateMessageViewService::markAsReadByCooperation($messagesToSetRead, $cooperation);
        } else {
            $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);
            PrivateMessageViewService::markAsReadByUser($messagesToSetRead, Hoomdossier::user(), $inputSource);
        }

        return response()->json([
            'unread' => PrivateMessageView::getTotalUnreadMessagesForCurrentRole(true, [['building_id', $buildingId]])[(int) $isPublic] ?? 0,
        ]);
    }
}
