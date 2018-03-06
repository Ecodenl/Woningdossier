<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use Closure;

class CooperationMiddleware
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
    	$coopSlug = $request->route()->parameter('cooperation');

    	\Log::debug($coopSlug);
    	$cooperation = Cooperation::where('slug', $coopSlug)->first();

    	if (!$cooperation instanceof Cooperation){
    		// No valid cooperation subdomain. Return to global index.
    		return redirect()->route('index');
	    }

	    \Log::debug("Session: cooperation -> " . $cooperation->id);
	    \Session::put('cooperation', $cooperation->id);

        return $next($request);
    }
}
