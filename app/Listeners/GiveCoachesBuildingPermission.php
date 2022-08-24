<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;

class GiveCoachesBuildingPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $building = $event->building;

        // the coaches can only get building permission when the user allowed it.
        $building->user->update([
            'allow_access' => true,
        ]);

        // first we check if a public conversation exists, if so we need to set the allowed access to true
        // and we need to check if there were "connected" coaches, if so we have to give them building permissions
        if (PrivateMessage::public()->conversation($building->id)->exists()) {
            // get all the coaches that are currently connected to the building
            $coachesWithAccessToResidentBuildingStatuses = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);

            // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
            foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
                BuildingPermissionService::givePermission(
                    User::find($coachWithAccessToResidentBuildingStatus->coach_id),
                    Building::find($coachWithAccessToResidentBuildingStatus->building_id)
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
                    'message' => 'Welkom bij het Hoomdossier, hier kunt u chatten met de coöperatie.',
                    'building_id' => $building->id,
                ]);
            });

            // give the user a unread message.
            PrivateMessageView::create([
                'input_source_id' => InputSource::findByShort(InputSource::RESIDENT_SHORT)->id,
                'private_message_id' => $privateMessage->id,
                'user_id' => $building->user->id,
            ]);
        }
    }
}
