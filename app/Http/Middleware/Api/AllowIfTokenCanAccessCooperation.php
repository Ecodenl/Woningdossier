<?php

namespace App\Http\Middleware\Api;

use App\Models\Client;
use App\Models\User;
use Closure;

class AllowIfTokenCanAccessCooperation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cooperation = $request->route('cooperation');

        abort_if($request->user()->tokenCannot("access:{$cooperation->slug}"), 403, "Unauthorized for current cooperation.");

        return $next($request);
    }
}
