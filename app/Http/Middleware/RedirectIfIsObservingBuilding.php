<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use Closure;
use Illuminate\Support\Facades\Log;

class RedirectIfIsObservingBuilding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (HoomdossierSession::isUserObserving()) {
            $user = Hoomdossier::user();
            Log::debug(__CLASS__ . '::' . __METHOD__);
            Log::debug("Middleware: user id: {$user->id} tried to access {$request->route()->uri} while observing");

            return redirect()->back();
        }

        return $next($request);
    }
}
