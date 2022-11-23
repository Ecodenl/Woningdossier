<?php

namespace App\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterResponse implements \Laravel\Fortify\Contracts\RegisterResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        if (Auth::guard()->user()->hasVerifiedEmail()) {
            Auth::guard()->logout();
            return redirect()->route('cooperation.auth.login')
                ->with('success', __('auth.register.form.message.account-connected'));
        }

        return redirect()
            ->route('cooperation.auth.verification.notice');
    }
}
