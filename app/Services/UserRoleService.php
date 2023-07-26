<?php

namespace App\Services;

use App\Helpers\RoleHelper;
use App\Models\Role;

class UserRoleService
{
    public Role $currentRole;

    public function forCurrentRole(Role $role): self
    {
        $this->currentRole = $role;
        return $this;
    }

    /**
     * Method returns the roles a specific role is allowed to see
     *
     * @return array
     */
    public function getViewableRoles(): array
    {
        return [
            RoleHelper::ROLE_COACH => [
                RoleHelper::ROLE_RESIDENT,
                RoleHelper::ROLE_COACH,
                RoleHelper::ROLE_COORDINATOR,
                RoleHelper::ROLE_COOPERATION_ADMIN,
            ],
            RoleHelper::ROLE_SUPER_ADMIN => [
                RoleHelper::ROLE_RESIDENT,
                RoleHelper::ROLE_COACH,
                RoleHelper::ROLE_COORDINATOR,
                RoleHelper::ROLE_COOPERATION_ADMIN,
            ],
            RoleHelper::ROLE_COORDINATOR => [
                RoleHelper::ROLE_RESIDENT,
                RoleHelper::ROLE_COACH,
                RoleHelper::ROLE_COORDINATOR,
                RoleHelper::ROLE_COOPERATION_ADMIN,
            ],
            RoleHelper::ROLE_COOPERATION_ADMIN => [
                RoleHelper::ROLE_RESIDENT,
                RoleHelper::ROLE_COACH,
                RoleHelper::ROLE_COORDINATOR,
                RoleHelper::ROLE_COOPERATION_ADMIN
            ],
        ][$this->currentRole->name] ?? [];
    }

    public function canView(Role $roleToView): bool
    {
        $roles = $this->getViewableRoles();
        if (in_array($roleToView->name, $roles)) {
            return true;
        }
        return false;
    }

    /**
     * Method returns the roles the user can manage
     *
     * @return array
     */
    public function getManageableRoles(): array
    {
        return [
                RoleHelper::ROLE_SUPER_ADMIN => [
                    RoleHelper::ROLE_RESIDENT,
                    RoleHelper::ROLE_COACH,
                    RoleHelper::ROLE_COORDINATOR,
                    RoleHelper::ROLE_COOPERATION_ADMIN,
                ],
                RoleHelper::ROLE_COORDINATOR => [
                    RoleHelper::ROLE_RESIDENT,
                    RoleHelper::ROLE_COACH,
                    RoleHelper::ROLE_COORDINATOR
                ],
                RoleHelper::ROLE_COOPERATION_ADMIN => [
                    RoleHelper::ROLE_RESIDENT,
                    RoleHelper::ROLE_COACH,
                    RoleHelper::ROLE_COORDINATOR,
                    RoleHelper::ROLE_COOPERATION_ADMIN
                ],
            ][$this->currentRole->name] ?? [];
    }

    public function canManage(Role $roleToManage): bool
    {
        $roles = $this->getManageableRoles();
        if (in_array($roleToManage->name, $roles)) {
            return true;
        }
        return false;
    }
}