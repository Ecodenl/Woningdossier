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

        // No valid cooperation subdomain. try to obtain it from the header.
        if (!$cooperation instanceof Cooperation) {
            $cooperation = Cooperation::whereSlug($request->header('X-Cooperation-Slug'))->first();
            // no cooperation in header, return to index.
            if (!$cooperation instanceof Cooperation) {
                return redirect()->route('index');
            }
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
