<?php

namespace App\Services;

use App\Models\BuildingPermission;

class BuildingPermissionService {


    /**
     * Delete the building permission for a coach and specific building
     *
     * @param $coachId
     * @param $buildingId
     * @return bool
     */
    public static function revokePermission($userId, $buildingId) : bool
    {
        BuildingPermission::where('user_id', $userId)->where('building_id', $buildingId)->delete();

        return true;
    }
}