<?php

namespace App\Services;

use App\Models\BuildingPermission;

class BuildingPermissionService
{
    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * Delete the building permission for a coach and specific building.
     *
     * @param $userId
     * @param $buildingId
     *
     * @return bool
     */
    public static function revokePermission($userId, $buildingId): bool
    {
        BuildingPermission::where('user_id', $userId)->where('building_id', $buildingId)->delete();

        return true;
    }

    /**
     * Give a user permission to a building.
     *
     * @param $userId
     * @param $buildingId
     *
     * @return bool
     */
    public static function givePermission($userId, $buildingId)
    {
        BuildingPermission::create([
            'user_id' => $userId, 'building_id' => $buildingId,
        ]);

        return true;
    }
}
