<?php

namespace App\Services;

use App\Models\BuildingCoachStatus;

class BuildingCoachStatusService
{
    /**
     * Add a building coach status with status removed, this will lead to "revoked access".
     *
     * @param $coachId
     * @param $buildingId
     *
     * @return bool
     */
    public static function revokeAccess($coachId, $buildingId): bool
    {
        BuildingCoachStatus::create([
            'coach_id' => $coachId, 'building_id' => $buildingId, 'status' => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        return true;
    }

    /**
     * Give the user / coach a active building status, which grants him access to messages the resident and add details.
     * does not give the user permission to access the building.
     *
     * @param $userId
     * @param $buildingId
     *
     * @return bool
     */
    public static function giveAccess($userId, $buildingId): bool
    {
        // We first set the status pending, we do this so we can count the pending statuses end the removed status to determine
        // if a coach has access
        BuildingCoachStatus::create([
            'coach_id' => $userId,
            'building_id' => $buildingId,
            'status' => BuildingCoachStatus::STATUS_PENDING,
        ]);

        // then we set the status in progress.
        // we cant use this status to count it since the coach, coordinator and cooperation admin would be able to set it again
        // and that would mess up the counting.
        BuildingCoachStatus::create([
            'coach_id' => $userId,
            'building_id' => $buildingId,
            'status' => BuildingCoachStatus::STATUS_IN_PROGRESS,
        ]);

        return true;
    }
}
