<?php

namespace App\Listeners;

use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\User;
use App\Services\BuildingCoachStatusService;

class RevokeBuildingPermissionForCoaches
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
     */
    public function handle(object $event): void
    {
        /** @var Building $building */
        $building = $event->building;

        $building->user->update([
            'allow_access' => false,
        ]);

        // get all the connected coaches to the building
        $connectedCoachesToBuilding = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building, true);

        // and revoke them the access to the building
        foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
            BuildingCoachStatusService::revokeAccess($connectedCoachToBuilding->coach, $building);
        }

        // Delete all the building permissions for this building
        $building->buildingPermissions()->delete();
    }
}
