<?php

namespace App\Policies;

use App\Helpers\Hoomdossier;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\UserActionPlanAdvice;
use App\Services\DiscordNotifier;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class UserActionPlanAdvicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user action plan advice.
     */
    public function view(Account $user, UserActionPlanAdvice $userActionPlanAdvice): bool
    {
        // Gets the user(s?) for the current session cooperation.
        $authorize = $user->users()->where('id', $userActionPlanAdvice->user_id)->exists();

        // we want to log this, as this is not meant to happen,
        // it means the user is trying to mess up hoomdossier
        // or something is wrong with our own code.
        if (! $authorize) {
            // Okay, so the user isn't a direct owner of the advice, but perhaps it's a coach, editing for the user...
            // We check if the user has permission to edit as a coach
            $building = $userActionPlanAdvice->user->building;
            if ($user->can('access-building', $building) && Hoomdossier::user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COACH)) {
                return true;
            }

            // TODO: With the commit where this todo is included, this should NO LONGER be happening.
            $userId = Hoomdossier::user()->id;
            (new DiscordNotifier())->notify("Account id {$user->id} with **current** user id {$userId} tried to access (denied) `{$userActionPlanAdvice->toJson()}`");
        }
        return $authorize;
    }
}
