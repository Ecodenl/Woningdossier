<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Closure;
use Illuminate\Support\Facades\URL;

class CooperationMiddleware
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
        $cooperation = $request->route()->parameter('cooperation');

        if (! $cooperation instanceof Cooperation) {
            // No valid cooperation subdomain. Return to global index.

            return redirect()->route('index');
        }
        if ('vrijstadenergie' == $cooperation->slug) {
            return redirect()->route('cooperation.welcome', ['cooperation' => 'energieloketrivierenland']);
        }

        HoomdossierSession::setCooperation($cooperation);

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            if ($cooperation instanceof Cooperation) {
                URL::defaults(['cooperation' => $cooperation->slug]);
            }
        }

        return $next($request);
    }
}
