<?php

namespace App\Helpers;

use Spatie\Permission\Models\Role;

class RoleHelper
{
    /**
     * Get the right route / url by a role name.
     *
     * @param string $roleName
     * @param bool Whether or not to check the user's role against the role name. Defaults to true.
     *
     * @return string The target url
     */
    public static function getUrlByRoleName(string $roleName, $checkUser = true)
    {
        // check if the user his role exists / is his
        if (!$checkUser || (\Auth::check() && \Auth::user()->roles()->where('name', $roleName)->first() instanceof Role)) {
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
                    return route('cooperation.admin.super-admin.index');
                    break;
                    //break;
                default:
                    return route('cooperation.home');
                    break;
            }
        }

        return route('cooperation.home');
    }

	/**
	 * Get the right route / url by a role
	 *
	 * @param Role $role
	 * @param bool Whether or not to check the user's role against the role name. Defaults to true.
	 *
	 * @return string
	 */
    public static function getUrlByRole(Role $role, $checkUser = true)
    {
        return self::getUrlByRoleName($role->name, $checkUser);
    }
}
