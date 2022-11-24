<?php

namespace App\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Fortify;

class LogoutResponse implements \Laravel\Fortify\Contracts\LogoutResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $cooperation = $request->route('cooperation');

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect(Fortify::redirects(route('cooperation.auth.login', compact('cooperation'))));
    }
}
