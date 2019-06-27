<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Scopes\GetValueScope;

class UserService
{
    public static function deleteUser(User $user)
    {

        // if the user is only associated with 1 cooperation, we delete the whole account with all its data
        // else we delete the relation between the cooperation and the user.
        if ($user->cooperations()->count() === 1) {

            $building = $user->buildings()->first();

            /* @var Building */
            if ($building instanceof Building) {
                $building->delete();
            }

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
            //$user->progress()->delete();
            // delete the cooperation from the user, belongsToMany so no deleting here.
            $user->cooperations()->detach();


            // finally remove the user itself :(
            $user->delete();
        } else {

            // get the user its first building
            $building = $user->buildings()->first();

            // delete the relation between the cooperation and the user
            $currentCooperation = Cooperation::find(HoomdossierSession::getCooperation());

            // first detach the roles from the user in its current cooperation.
            $user->roles()->detach($user->roles);
            // now we can safely remove the cooperation itself from the user.
            $user->cooperations()->detach($currentCooperation);

            /* @var Building */
            if ($building instanceof Building) {
                // delete the building its private messages, on the current cooperation (cooperationScope)
                $building->privateMessages()->delete();
            }


            // we only want to do this if the user is deleting himself. Otherwise admins would randomly logout.
            if (\Auth::id() == $user->id) {
                // the user still exists, so we have to logout the user
                HoomdossierSession::destroy();
                \Auth::logout();
                request()->session()->invalidate();
            }
        }
    }
}
