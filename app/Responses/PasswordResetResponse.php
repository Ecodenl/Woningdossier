<?php

namespace App\Responses;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Fortify;

class PasswordResetResponse implements \Laravel\Fortify\Contracts\PasswordResetResponse
{
    /**
     * The response status language key.
     *
     * @var string
     */
    protected $status;

    /**
     * Create a new response instance.
     *
     * @param  string  $status
     * @return void
     */
    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse(Request $request): Response
    {
        $cooperation = $request->route('cooperation');

        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($this->status)], 200)
            : redirect(Fortify::redirects('password-reset', route('cooperation.auth.login', compact('cooperation'))))
                ->with('status', trans($this->status));
    }
}
