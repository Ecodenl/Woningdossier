<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;

class RedirectIfTwoFactorAuthenticatable extends \Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable
{
    protected function twoFactorChallengeResponse($request, $user)
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return $request->wantsJson()
            ? response()->json(['two_factor' => true])
            : redirect()->route('cooperation.auth.two-factor.login');
    }
}