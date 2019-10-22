<?php

namespace App\Listeners;

use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;

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

        // update all messages with allow_access to true.
        $conversationRequests = PrivateMessage::conversationRequestByBuildingId($building->id);
        $conversationRequests->update(['allow_access' => true]);

        // get all the coaches that are currently connected to the building
        $coachesWithAccessToResidentBuildingStatuses = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);

        // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
        foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
            BuildingPermission::create([
                'user_id' => $coachWithAccessToResidentBuildingStatus->coach_id,
                'building_id' => $coachWithAccessToResidentBuildingStatus->building_id,
            ]);
        }
    }
}
