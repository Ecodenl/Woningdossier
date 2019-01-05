<?php

namespace App\Helpers;

use Spatie\Permission\Models\Role;

class RoleHelper
{
    /**
     * Get the right route / url by a role name.
     *
     * @param $roleName
     *
     * @return string The target url
     */
    public static function getUrlByRoleName(string $roleName)
    {
        // check if the user his role exists / is his
        if (\Auth::check() && \Auth::user()->roles()->where('name', $roleName)->first() instanceof Role) {
            switch ($roleName) {
                case 'cooperation-admin':
                    return route('cooperation.admin.cooperation.cooperation-admin.index');
                    break;
                case 'coordinator':
                    return route('cooperation.admin.cooperation.coordinator.index');
                    break;
                case 'coach':
                    return route('cooperation.admin.coach.index');
                    break;
                case 'superuser':
                    // for now: fall through
                    return route('cooperation.admin.cooperation.cooperation-admin.index',
                        ['role_name' => $roleName]);
                    break;
                    //break;
                case 'super-admin':
                    // for now: fall through
                    return route('cooperation.admin.cooperation.cooperation-admin.index',
                        ['role_name' => $roleName]);
                    break;
                    //break;
                default:
                    return route('cooperation.tool.index');
                    break;
            }
        }

        return route('cooperation.tool.index');
    }

    public static function getUrlByRole(Role $role)
    {
        return self::getUrlByRoleName($role->name);
    }
}
