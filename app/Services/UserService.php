<?php

namespace App\Services;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Scopes\GetValueScope;

class UserService
{
    public static function deleteUser(User $user)
    {

        $building = $user->building;

        $building->delete();

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the user interests
        $user->interests()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the energy habits from a user
        $user->energyHabit()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // remove the notification settings
        $user->notificationSettings()->delete();
        // remove the progress from a user
        $building->progress()->delete();
        // first detach the roles from the user
        $user->roles()->detach($user->roles);
        // delete the private messages from the cooperation
        $building->privateMessages()->delete();

        // remove the user itself.
        $user->delete();

        // if the account has no users anymore then we delete the account itself to.
        if (Hoomdossier::account()->users()->withoutGlobalScopes()->count() == 0) {
            // bye !
            Hoomdossier::account()->delete();
        }
    }

}
