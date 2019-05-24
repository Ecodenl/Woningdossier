<?php

namespace App\Http\Middleware;

use App\Exceptions\RoleInSessionHasNoAssociationWithUser;
use App\Helpers\HoomdossierSession;
use App\Models\Role;
use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CurrentRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {

        // check if the user is actually authorized at all.
        if (\Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        $authorizedRole = Role::findById(HoomdossierSession::getRole());

        // we will need to check if the user actually has the role thats set in the session.
        // this will occur for example, if a admin removes a coach role etc while the coach is authorized in the tool.
        if (!\Auth::user()->hasRole($authorizedRole)) {
            throw RoleInSessionHasNoAssociationWithUser::forRole($authorizedRole);
        }

        // if the user has multiple roles, while trying to access a url he is not authorized to
        // then it seems like a good idea to let him choose a role.
//        if (\Auth::user()->hasMultipleRoles() && !\Auth::user()->hasRoleAndIsCurrentRole($roles)) {
//            return redirect(route('cooperation.admin.index'));
//        }


        // check if the user has the role and if it is his current role.
        if (!\Auth::user()->hasRoleAndIsCurrentRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }



        return $next($request);
    }
}
