<?php 

namespace App\Helpers;

use Spatie\Permission\Traits\HasRoles;

class RoleHelper
{
    use HasRoles;

    /**
     * Get the right route by a role name
     *
     * @param $roleName
     * @return string
     */
    public static function getUrlByRoleName(string $roleName)
    {
        if ($roleName == "cooperation-admin") {
            return route('cooperation.admin.cooperation.cooperation-admin.index', ['role_name' => $roleName]);
        }
        else if ($roleName == "coordinator") {
            return route('cooperation.admin.cooperation.coordinator.index', ['role_name' => $roleName]);
        }
        else if ($roleName == "coach") {
            return route('cooperation.admin.coach.index', ['role_name' => $roleName]);
        }
        else {
            return route('cooperation.admin.index');
        }
    }
}