<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function redirectTo($request)
    {
        // Apparently the route binding has not happened yet, so we manually set it to the route.
        $params = ['cooperation' => $request->route('cooperation')];

        if ($request->route()?->getName() === 'cooperation.auth.verification.verify') {
            // So a user is trying to verify his account but isn't logged in. We will pass the account ID onward
            // so the form can autofill the email for this user.
            $params['id'] = $request->route('id');

            // We will warn them requiring to log in first.
            session()->flash('status', __('cooperation/auth/verify.require-auth'));
        }

        if (! $request->expectsJson()) {
            return route('cooperation.auth.login', $params);
        }
    }
}
