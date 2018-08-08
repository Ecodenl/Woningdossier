<?php 

namespace App\Helpers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RoleHelper
{
    use HasRoles;

    /**
     * Get the right route / url by a role name
     *
     * @param $roleName
     * @return string | url
     */
    public static function getUrlByRoleName(string $roleName)
    {
        // check if the user his role exists / is his
        if (\Auth::user()->roles()->where('name', $roleName)->first() instanceof Role) {
            if ($roleName == "cooperation-admin") {
                return route('cooperation.admin.cooperation.cooperation-admin.index', ['role_name' => $roleName]);
            }
            else if ($roleName == "coordinator") {
                return route('cooperation.admin.cooperation.coordinator.index', ['role_name' => $roleName]);
            }
            else if ($roleName == "coach") {
                return route('cooperation.admin.coach.index', ['role_name' => $roleName]);
            }
        }

        return route('cooperation.tool.index');
    }
}