<?php

namespace App\Services;

use App\Models\Building;
use App\Models\User;
use App\Scopes\GetValueScope;

class UserService
{
    public static function deleteUser(User $user)
    {
        $building = $user->buildings()->first();

        /* @var Building */
        if ($building instanceof Building) {
            $building->delete();
        }

        // remove the building usages from the user
        $user->buildingUsage()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the action plan advices from the user
        $user->actionPlanAdvices()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the user interests
        $user->interests()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the energy habits from a user
        $user->energyHabit()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // remove the progress from a user
        //$user->progress()->delete();
        // delete the cooperation from the user, belongsToMany so no deleting here.
        $user->cooperations()->detach();

        // finally remove the user itself :(
        $user->delete();
    }
}
