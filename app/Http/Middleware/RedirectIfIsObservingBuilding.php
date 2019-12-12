<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use Closure;
use Illuminate\Support\Facades\Log;

class RedirectIfIsObservingBuilding
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
        if (HoomdossierSession::isUserObserving()) {
            Log::debug(__CLASS__.'::'.__METHOD__);
            Log::debug("Middleware: user id: {$request->user()->id} tried to access {$request->route()->uri} while observing");
            return redirect()->back();
        }

        return $next($request);
    }
}
