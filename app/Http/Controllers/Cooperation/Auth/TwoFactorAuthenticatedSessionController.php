<?php

namespace App\Http\Controllers\Cooperation\Auth;

use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController as FortifiesTwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

class TwoFactorAuthenticatedSessionController extends FortifiesTwoFactorAuthenticatedSessionController
{
    /**
     * Show the two factor authentication challenge view.
     */
    public function create(TwoFactorLoginRequest $request): TwoFactorChallengeViewResponse
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('cooperation.auth.login'));
        }

        return app(TwoFactorChallengeViewResponse::class);
    }
}
