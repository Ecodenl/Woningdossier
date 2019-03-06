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
        BuildingCoachStatus::create([
            'coach_id' => $userId,
            'building_id' => $buildingId,
            'status' => BuildingCoachStatus::STATUS_ACTIVE,
        ]);

        return true;
    }
}
