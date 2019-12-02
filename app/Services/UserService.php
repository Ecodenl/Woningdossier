<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use App\Scopes\GetValueScope;

class UserService
{
    public static function deleteUser(User $user, $shouldForceDeleteBuilding = false)
    {
        $accountId = $user->account_id;
        $building = $user->building;

        if ($building instanceof Building) {
            if ($shouldForceDeleteBuilding) {
                BuildingService::deleteBuilding($building);
            } else {
                $building->delete();
            }
        }

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->withoutGlobalScopes()->delete();
        // remove the user interests
        $user->interests()->withoutGlobalScopes()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->withoutGlobalScopes()->delete();
        // remove the motivations from a user
        $user->motivations()->withoutGlobalScopes()->delete();
        // remove the notification settings
        $user->notificationSettings()->withoutGlobalScopes()->delete();
        // remove the progress from a user
        // first detach the roles from the user
        $user->roles()->detach($user->roles);


        // remove the user itself.
        // if the account has no users anymore then we delete the account itself too.
        if (0 == User::withoutGlobalScopes()->where('account_id', $accountId)->count()) {
            // bye !
            Account::find($accountId)->delete();
        }
    }
}
