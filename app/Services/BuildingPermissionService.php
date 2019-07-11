<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\User;

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
    public static function revokePermission(User $user, Building $building): bool
    {
        BuildingPermission::where('user_id', $user->id)->where('building_id', $building->id)->delete();

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
    public static function givePermission(User $user, Building $building)
    {
        BuildingPermission::create([
            'user_id' => $user->id, 'building_id' => $building->id,
        ]);

        return true;
    }
}
