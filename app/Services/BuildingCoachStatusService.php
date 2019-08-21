<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\User;

class BuildingCoachStatusService
{
    /**
     * Add a building coach status with status removed, this will lead to "revoked access".
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public static function revokeAccess(User $user, Building $building): bool
    {

        BuildingCoachStatus::create([
            'coach_id' => $user->id, 'building_id' => $building->id, 'status' => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        return true;
    }

    /**
     * Give the user / coach a added building status, which grants him access to messages the resident and add details.
     * does not give the user permission to access the building.
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public static function giveAccess(User $user, Building $building): bool
    {

        // Add the user with status added
        BuildingCoachStatus::create([
            'coach_id' => $user->id,
            'building_id' => $building->id,
            'status' => BuildingCoachStatus::STATUS_ADDED,
        ]);

        $building->setStatus('in_progress');

        return true;
    }
}
