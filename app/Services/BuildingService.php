<?php

namespace App\Services;

use App\Models\Building;

class BuildingService {

    public static function deleteBuilding(Building $building)
    {
        $building->progress()->withoutGlobalScopes()->delete();
        // delete the private messages from the cooperation
        $building->privateMessages()->withoutGlobalScopes()->delete();
        // table will be removed anyways.
        \DB::table('building_appliances')->whereBuildingId($building->id)->delete();

        $building->forceDelete();
    }
}