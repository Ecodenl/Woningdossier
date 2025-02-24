<?php

namespace App\Http\Middleware\Api;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Closure;

class AllowIfTokenCanAccessCooperation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $request->route('cooperation');

        /** @var Client $client */
        $client = $request->user();

        abort_if($client->tokenCannot("access:{$cooperation->slug}"), 403, "Unauthorized for current cooperation.");

        return $next($request);
    }
}
