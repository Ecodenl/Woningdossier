<?php

namespace App\Responses;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Fortify;

class LogoutResponse implements \Laravel\Fortify\Contracts\LogoutResponse
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): Response
    {
        $cooperation = $request->route('cooperation');

        return $request->wantsJson() ? new JsonResponse('', 204) : redirect(Fortify::redirects(route('cooperation.auth.login', compact('cooperation'))));
    }
}
