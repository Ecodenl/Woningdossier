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

    public function show(Account $account, Role $role, User $user, Role $currentUserRole)
    {
        return $this->userRoleService->forCurrentRole($currentUserRole)->canManage($role);
    }

    /**
     * @param  Account  $account
     * @param  Role  $role  the role that should be checked.
     * @param  User  $user  the authenticated user
     * @param  Role  $currentUserRole  the current role of the authenticated user.
     * @param  User  $userToGiveRole  the user that would receive the role
     * @return void
     */
    public function store(Account $account, Role $role, User $user, Role $currentUserRole, User $userToGiveRole)
    {
        $this->userRoleService->forCurrentRole($currentUserRole)->canManage($role);

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

    public function destroy(Account $account, Role $role, User $user, Role $currentUserRole, User $userToGiveRole)
    {
        if ($this->userRoleService->forCurrentRole($currentUserRole)->canManage($role)) {
            // a cooperation admin is not allowed to remove his own cooperation admin role.
            if ($user->id === $userToGiveRole->id && $role->name === RoleHelper::ROLE_COOPERATION_ADMIN) {
                return false;
            }
            return true;
        }
        return false;
    }
}
