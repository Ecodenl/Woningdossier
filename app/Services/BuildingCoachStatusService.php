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
     * @param $privateMessageId
     *
     * @return bool
     */
    public static function revokeAccess($coachId, $buildingId, $privateMessageId): bool
    {
        BuildingCoachStatus::create([
            'coach_id' => $coachId, 'building_id' => $buildingId, 'private_message_id' => $privateMessageId, 'status' => BuildingCoachStatus::STATUS_REMOVED,
        ]);

        return true;
    }
}
