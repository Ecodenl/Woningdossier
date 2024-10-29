<?php

namespace App\Policies;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\UserService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Check if a user is authorized to do admin stuff.
     */
    public function accessAdmin(User $user): bool
    {
        return $user->hasAnyRole(RoleHelper::ADMIN_ROLES);
    }

    public function sendUserInformationToEconobis(Account $account, User $user)
    {
        return app(UserService::class)->forUser($user)->isRelatedWithEconobis();
    }

    /**
     * Check if a user is authorized to delete a user.
     */
    public function deleteUser(Account $account, User $userToDelete): bool
    {
        $user = $account->user();
        // While a user is allowed to see his own stuff, he is not allowed to do anything in it.
        if ($userToDelete->id == Hoomdossier::user()->id) {
            return false;
        }

        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_SUPER_ADMIN, RoleHelper::ROLE_COOPERATION_ADMIN])
            && $userToDelete->id != $user->id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a user is authorize to delete his own account.
     */
    public function deleteOwnAccount(Account $account): bool
    {
        return ! $account->user()->hasRole([RoleHelper::ROLE_COOPERATION_ADMIN]);
    }

    /**
     * Check if a user is authorized to destroy a user.
     */
    public function destroy(Account $account, User $userToDestroy): bool
    {
        // check if the user can delete a user, and if the user to be destroyed is a member of the user his cooperation
        // remove the cooperations stuff
        return $this->deleteUser($account, $userToDestroy) && $userToDestroy->cooperation->id == HoomdossierSession::getCooperation();
    }

    /**
     * Check if a user is allowed to participate in a group chat or not.
     *
     * @param $buildingId
     */
    public function participateInGroupChat(Account $account, $buildingId): bool
    {
        $user = $account->user();
        // if the user is a coach and has a active building coach status, return true
        if ($user->hasRole(RoleHelper::ROLE_COACH) && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {
            return true;
        } elseif ($user->hasRole(RoleHelper::ROLE_RESIDENT) && HoomdossierSession::getBuilding() == $buildingId) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user can remove a participant from the group chat.
     */
    public function removeParticipantFromChat(Account $account, User $groupParticipant): bool
    {
        // A coordinator and resident can remove a coach from a conversation.
        // Also check if the current building ID is from the $groupParticipant, cause if so we can't remove him because he is the building owner
        return $account->user()->hasRoleAndIsCurrentRole([RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COACH, RoleHelper::ROLE_COOPERATION_ADMIN])
               && $groupParticipant->hasRole([RoleHelper::ROLE_COACH]) && $groupParticipant->building->id !== HoomdossierSession::getBuilding(false);
    }

    /**
     * Returns if a user can assign a particular role (just if the user is
     * allowed to assign roles).
     *
     * @param Role $role The role which is to be assigned
     */
    public function assignRole(Account $account, Role $role): bool
    {
        $user = $account->user();
        if ($user->hasRoleAndIsCurrentRole(RoleHelper::ROLE_SUPER_ADMIN)) {
            return in_array($role->name, [RoleHelper::ROLE_COOPERATION_ADMIN, RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT]);
        }
        if ($user->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COOPERATION_ADMIN)) {
            return in_array($role->name, [RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT]);
        }
        if ($user->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COORDINATOR)) {
            return in_array($role->name, [RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT]);
        }

        return false;
    }
}
