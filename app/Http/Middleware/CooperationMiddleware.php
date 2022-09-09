<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Closure;
use Illuminate\Support\Facades\Log;
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
        Log::info("DBG ". $request->url());
        $a = $request->route('cooperation');
        Log::info("DBG ". $a);
        $b = $request->route('cooperation');
        Log::info("DBG ". $b);

        $cooperation = $request->route()->parameter('cooperation');

        //Log::debug("DBG CooperationMiddleware : Cooperation = " . $cooperation);

        // if no valid cooperation is found, return to index
        if (!$cooperation instanceof Cooperation) {
            //Log::debug("DBG CooperationMiddleware : not instanceof Cooperation");
            return redirect()->route('index');
        }

        HoomdossierSession::setCooperation($cooperation);

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            URL::defaults(['cooperation' => $cooperation->slug]);
        }

        return $next($request);
    }
}
