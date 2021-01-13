<?php

namespace App\Policies;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\User;
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
        return $user->hasAnyRole(['coordinator', 'superuser', 'super-admin', 'coach', 'cooperation-admin']);
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

        if ($user->hasRoleAndIsCurrentRole(['super-admin', 'cooperation-admin']) && $userToDelete->id != $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a user is authorize to delete his own account.
     *
     * @return bool
     */
    public function deleteOwnAccount(Account $account)
    {
        return $account->user()->hasRole(['cooperation-admin']);
    }

    /**
     * Check if a user is authorized to destroy a user.
     *
     * @return bool
     */
    public function destroy(Account $account, User $userToDestroy)
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
        if ($user->hasRole('coach') && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {
            return true;
        } elseif ($user->hasRole('resident') && HoomdossierSession::getBuilding() == $buildingId) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user can remove a participant from the group chat.
     *
     * @param User $user             | Auth user
     * @param User $groupParticipant | Participant from the group chat
     */
    public function removeParticipantFromChat(Account $account, User $groupParticipant): bool
    {
        // a coordinator and resident can remove a coach from a conversation
        // also check if the current building id is from the $groupParticipant, cause ifso we cant remove him because he is the building owner
        return $account->user()->hasRoleAndIsCurrentRole(['resident', 'coordinator', 'cooperation-admin']) && $groupParticipant->hasRole(['coach']);
    }

    /**
     * Returns if a user can assign a particular role (just if the user is
     * allowed to assign roles).
     *
     * @param Role $role The role which is to be assigned
     *
     * @return bool
     */
    public function assignRole(Account $account, Role $role)
    {
        $user = $account->user();
        if ($user->hasRoleAndIsCurrentRole('super-admin')) {
            return true;
        }
        if ($user->hasRoleAndIsCurrentRole('cooperation-admin')) {
            return in_array($role->name, ['coordinator', 'coach', 'resident']);
        }
        if ($user->hasRoleAndIsCurrentRole('coordinator')) {
            return in_array($role->name, ['coordinator', 'coach', 'resident']);
        }

        return false;
    }
}
