<?php 

namespace App\Helpers;

use Spatie\Permission\Models\Role;

class RoleHelper
{
    /**
     * Get the right route / url by a role name
     *
     * @param $roleName
     * @return string The target url
     */
    public static function getUrlByRoleName(string $roleName)
    {
        // check if the user his role exists / is his
        if (\Auth::check() && \Auth::user()->roles()->where('name', $roleName)->first() instanceof Role) {
        	switch($roleName) {
		        case 'cooperation-admin':
			        return route( 'cooperation.admin.cooperation.cooperation-admin.index',
				        [ 'role_name' => $roleName ] );
			        break;
		        case 'coordinator':
			        return route( 'cooperation.admin.cooperation.coordinator.index',
				        [ 'role_name' => $roleName ] );
			        break;
		        case 'coach':
			        return route( 'cooperation.admin.coach.index',
				        [ 'role_name' => $roleName ] );
			        break;
		        case 'superuser':
		        	// for now: fall through
			        //break;
		        case 'super-admin':
		            // for now: fall through
			        //break;
		        default:
			        return route('cooperation.tool.index');
			        break;
	        }
        }
	    return route('cooperation.tool.index');
    }
}