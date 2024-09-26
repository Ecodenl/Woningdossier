<?php

namespace App\Policies;

use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public UserRoleService $userRoleService;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function view(Account $account, Role $role, User $user, Role $currentUserRole)
    {
        return $this->userRoleService->forCurrentRole($currentUserRole)->canView($role);
    }

    public function editAny(Account $account, Role $currentUserRole)
    {
        if (in_array($currentUserRole->name, [RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT])) {
            return false;
        }
        return true;
    }


    /**
     * @param  Account  $account
     * @param  Role  $role  the role that should be checked.
     * @param  User  $user  the authenticated user
     * @param  Role  $currentUserRole  the current role of the authenticated user.
     * @param  User  $userToGiveRole  the user that would receive the role
     */
    public function store(Account $account, Role $role, User $user, Role $currentUserRole, User $userToGiveRole)
    {
        if($this->userRoleService->forCurrentRole($currentUserRole)->canManage($role)) {
            // the cooperation-admin is the only one who can give himself other roles.
            if ($user->id === $userToGiveRole->id && $currentUserRole->name === RoleHelper::ROLE_COOPERATION_ADMIN) {
                return true;
            }
            return true;
        }
        return false;

        // this is the legacy of "assign-role" this can and should be removed when rewritten in a ok manner
//        if ($user->hasRoleAndIsCurrentRole('super-admin')) {
//            return in_array($role->name, ['cooperation-admin', 'coordinator', 'coach', 'resident']);
//        }
//        if ($user->hasRoleAndIsCurrentRole('cooperation-admin')) {
//            return in_array($role->name, ['coordinator', 'coach', 'resident']);
//        }
//        if ($user->hasRoleAndIsCurrentRole('coordinator')) {
//            return in_array($role->name, ['coordinator', 'coach', 'resident']);
//        }

    }

    public function delete(Account $account, Role $role, User $user, Role $currentUserRole, User $userToRemoveRoleFrom)
    {
        // its not possible to delete the user its only available role
        if ($userToRemoveRoleFrom->hasNotMultipleRoles() && $role->id === $userToRemoveRoleFrom->roles->first()?->id) {
            return false;
        }
        if ($this->userRoleService->forCurrentRole($currentUserRole)->canManage($role)) {
            // a cooperation admin is not allowed to remove his own cooperation admin role.
            if ($user->id === $userToRemoveRoleFrom->id && $role->name === RoleHelper::ROLE_COOPERATION_ADMIN) {
                return false;
            }
            return true;
        }
        return false;
    }
}
