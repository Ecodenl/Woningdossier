<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use Closure;

class RedirectIfIsObservingOtherBuilding
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
            return redirect(route('cooperation.tool.general-data.index'));
        }

        return $next($request);
    }
}
