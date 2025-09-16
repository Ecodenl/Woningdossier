<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class RoleHelper
{
    const string ROLE_SUPERUSER = 'superuser';
    const string ROLE_SUPER_ADMIN = 'super-admin';
    const string ROLE_COOPERATION_ADMIN = 'cooperation-admin';
    const string ROLE_COACH = 'coach';
    const string ROLE_RESIDENT = 'resident';
    const string ROLE_COORDINATOR = 'coordinator';

    const array ADMIN_ROLES = [
        self::ROLE_COORDINATOR,
        self::ROLE_SUPERUSER,
        self::ROLE_SUPER_ADMIN,
        self::ROLE_COACH, // TODO: Check if we should keep this one here, not really an admin role
        self::ROLE_COOPERATION_ADMIN
    ];

    /**
     * Get the right route / url by a role name.
     *
     * @return string The target url
     */
    public static function getUrlByRoleName(string $roleName): string
    {
        $redirectMap = [
            'cooperation-admin' => 'cooperation.admin.cooperation.cooperation-admin.index',
            'coordinator' => 'cooperation.admin.cooperation.coordinator.index',
            'coach' => 'cooperation.admin.coach.index',
            'super-admin' => 'cooperation.admin.super-admin.index',
            'resident' => 'cooperation.home',
        ];

        $user = Hoomdossier::user();
        /** @var Role|null $role */
        $role = $user?->roles()->where('name', $roleName)->first();

        if ($user instanceof User && $role instanceof Role) {
            // the resident may be redirect to his last visited url.
            if ($roleName === self::ROLE_RESIDENT && ! empty($user->last_visited_url)) {
                // For now just local; if host mismatches (e.g. due to DB dump) don't redirect externally
                if (app()->isLocal() && request()->getHost() !== parse_url($user->last_visited_url, PHP_URL_HOST)) {
                    return route($redirectMap[$roleName]);
                }
                return $user->last_visited_url;
            }
            return route($redirectMap[$roleName]);
        }

        Log::debug("check user is true");
        return route('cooperation.home');
    }


    /**
     * Get the right route / url by a role.
     */
    public static function getUrlByRole(Role $role): string
    {
        return self::getUrlByRoleName($role->name);
    }
}
