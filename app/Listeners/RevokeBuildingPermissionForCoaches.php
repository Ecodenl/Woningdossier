<?php

namespace App\Listeners;

use App\Events\UserRevokedAccessToHisBuilding;
use App\Models\Building;
use App\Services\BuildingCoachStatusService;

class RevokeBuildingPermissionForCoaches
{
    /**
     * Handle the event.
     */
    public function handle(UserRevokedAccessToHisBuilding $event): void
    {
        /** @var Building $building */
        $building = $event->building;

        $building->user->update([
            'allow_access' => false,
        ]);

        // get all the connected coaches to the building
        $connectedCoachesToBuilding = BuildingCoachStatusService::getConnectedCoachesByBuilding($building, true);

        // and revoke them the access to the building
        foreach ($connectedCoachesToBuilding as $connectedCoachToBuilding) {
            BuildingCoachStatusService::revokeAccess($connectedCoachToBuilding->coach, $building);
        }

        // Delete all the building permissions for this building
        $building->buildingPermissions()->delete();
    }
}
