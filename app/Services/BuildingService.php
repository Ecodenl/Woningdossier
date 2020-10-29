<?php

namespace App\Services;

use App\Models\Building;

class BuildingService
{
    public static function deleteBuilding(Building $building)
    {
        $building->completedSteps()->withoutGlobalScopes()->delete();
        // delete the private messages from the cooperation
        $building->privateMessages()->withoutGlobalScopes()->delete();

        $building->stepComments()->withoutGlobalScopes()->delete();

        // table will be removed anyways.
        \DB::table('building_appliances')->whereBuildingId($building->id)->delete();

        $building->forceDelete();
    }
}
