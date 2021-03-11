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
        if (! $request->expectsJson()) {
            // apparently the route binding has not happened yet.
            return route('cooperation.auth.login', ['cooperation' => $request->route('cooperation')]);
        }
    }
}
