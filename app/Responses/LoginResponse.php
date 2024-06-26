<?php

namespace App\Responses;

use App\Helpers\RoleHelper;
use App\Helpers\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class LoginResponse implements \Laravel\Fortify\Contracts\LoginResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // the guard()->user() will return the auth model, in our case this is the Account model
        // but we want the user from the account, so that's why we do ->user()->user();
        $user = Auth::guard()->user()->user();
        $role = Role::findByName($user->roles()->first()->name);

        $redirect = 1 == $user->roles->count() ? RoleHelper::getUrlByRole($role) : '/admin';

        $intended = session()->pull('url.intended');

        // If it's the e-mail verify, we will direct them to it.
        if (! empty($intended) && Str::startsWith(parse_url($intended, PHP_URL_PATH), '/email/verify')) {
            $redirect = $intended;
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->to($redirect);
    }
}
