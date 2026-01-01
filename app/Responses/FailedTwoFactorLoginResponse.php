<?php

namespace App\Responses;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;

class FailedTwoFactorLoginResponse implements FailedTwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): Response
    {
        [$key, $message] = $request->filled('recovery_code') ? ['recovery_code', __('validation.custom.recovery_code')] : ['code', __('validation.custom.code')];

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                $key => [$message],
            ]);
        }

        return to_route('cooperation.auth.two-factor.login')
            ->withErrors([$key => $message]);
    }
}
