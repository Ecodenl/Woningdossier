<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use Closure;

class TrackVisitedUrl
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
        $user = Hoomdossier::user();
        $user->update(['last_visited_url' => $request->fullUrl()]);

        return $next($request);
    }
}
