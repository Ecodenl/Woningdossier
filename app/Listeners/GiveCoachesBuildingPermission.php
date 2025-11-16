<?php

namespace App\Listeners;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;

class GiveCoachesBuildingPermission
{
    /**
     * Handle the event.
     */
    public function handle(UserAllowedAccessToHisBuilding $event): void
    {
        $user = $event->user;
        $building = $event->building;

        // the coaches can only get building permission when the user allowed it.
        $user->update([
            'allow_access' => true,
        ]);

        // first we check if a public conversation exists, if so we need to set the allowed access to true
        // and we need to check if there were "connected" coaches, if so we have to give them building permissions
        if (PrivateMessage::public()->conversation($building->id)->exists()) {
            // get all the coaches that are currently connected to the building
            $coachesWithAccessToResidentBuildingStatuses = BuildingCoachStatusService::getConnectedCoachesByBuilding($building, true);

            // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
            foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
                BuildingPermissionService::givePermission(
                    $coachWithAccessToResidentBuildingStatus->coach,
                    $building
                );
            }
        } else {
            $cooperation = HoomdossierSession::getCooperation(true);

            $privateMessage = PrivateMessage::withoutEvents(function () use ($cooperation, $building) {
                return PrivateMessage::create([
                    'is_public' => true,
                    'from_cooperation_id' => $cooperation->id,
                    'to_cooperation_id' => $cooperation->id,
                    'from_user' => $cooperation->name,
                    'message' => 'Welkom in het Hoomdossier, hier kun je chatten met jouw energiecoach.',
                    'building_id' => $building->id,
                ]);
            });

            // give the user an unread message.
            PrivateMessageView::create([
                'input_source_id' => InputSource::findByShort(InputSource::RESIDENT_SHORT)->id,
                'private_message_id' => $privateMessage->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
