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
     * Determine whether the user can view any user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @return mixed
     */
    public function viewAny(Account $user)
    {
        //
    }

    /**
     * Determine whether the user can view the user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @return mixed
     */
    public function view(Account $user, UserActionPlanAdvice $userActionPlanAdvice): bool
    {
        $authorize = $user->users()->pluck('id')->contains($userActionPlanAdvice->user_id);

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

            $userId = Hoomdossier::user()->id;
            (new DiscordNotifier())->notify("Account id {$user->id} with **current** user id {$userId} tried to access (denied) `{$userActionPlanAdvice->toJson()}`");
        }
        return $authorize;
    }

    /**
     * Determine whether the user can create user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @return mixed
     */
    public function create(Account $user)
    {
        //
    }

    /**
     * Determine whether the user can update the user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @return mixed
     */
    public function update(Account $user, UserActionPlanAdvice $userActionPlanAdvice)
    {
        //
    }

    /**
     * Determine whether the user can delete the user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @return mixed
     */
    public function delete(Account $user, UserActionPlanAdvice $userActionPlanAdvice)
    {
        //
    }

    /**
     * Determine whether the user can restore the user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @return mixed
     */
    public function restore(Account $user, UserActionPlanAdvice $userActionPlanAdvice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the user action plan advice.
     *
     * @param  \App\Models\Account  $user
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @return mixed
     */
    public function forceDelete(Account $user, UserActionPlanAdvice $userActionPlanAdvice)
    {
        //
    }
}
