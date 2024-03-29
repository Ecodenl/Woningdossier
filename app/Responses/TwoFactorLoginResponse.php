<?php

namespace App\Responses;

use App\Helpers\RoleHelper;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Spatie\Permission\Models\Role;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // the $request->user() will return the auth model, in our case this is the Account model
        // but we want the user from the account, so that's why we do ->user()->user();
        $user = $request->user()->user();
        $role = Role::findByName($user->roles()->first()->name);

        $redirect = 1 == $user->roles->count() ? RoleHelper::getUrlByRole($role) : '/admin';

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($redirect);
    }
}
