<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $user = Hoomdossier::account();
        if (!$user || ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::route('cooperation.auth.verification.notice');
        }

        return $next($request);
    }
}
