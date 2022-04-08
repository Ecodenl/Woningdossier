<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use Closure;
use Illuminate\Support\Facades\Log;

class RedirectIfIsObservingBuilding
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (HoomdossierSession::isUserObserving()) {
            $user = Hoomdossier::user();
            Log::debug(__CLASS__.'::'.__METHOD__);
            Log::debug("Middleware: user id: {$user->id} tried to access {$request->route()->uri} while observing");

            return redirect()->back();
        }

        return $next($request);
    }
}
