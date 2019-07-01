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
            // remove the notification settings
            $user->notificationSettings()->delete();
            // remove the progress from a user

            // first detach the roles from the user in its current cooperation.
            $user->roles()->detach($user->roles);

            // delete the private messages from the cooperation
            $building->privateMessages()->delete();


            // remove the user itself.
            $user->delete();

            // we only want to do this if the user is deleting himself. Otherwise admins would randomly logout.
            if (Hoomdossier::user()->id == $user->id) {
                // the user still exists, so we have to logout the user
                HoomdossierSession::destroy();
                \Auth::logout();
                request()->session()->invalidate();
            }
    }
}
