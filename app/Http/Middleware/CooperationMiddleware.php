<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use Closure;
use Illuminate\Support\Facades\URL;

class CooperationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cooperation = $request->route()->parameter('cooperation');

        if (! $cooperation instanceof Cooperation) {
            // No valid cooperation subdomain. Return to global index.
            \Log::debug('No cooperation found');

            return redirect()->route('index');
        }

        \Log::debug('Session: cooperation -> '.$cooperation->id.' ('.$cooperation->slug.')');
        \Session::put('cooperation', $cooperation->id);

        // Set as default URL parameter
        if ($request->session()->has('cooperation')) {
            //$cooperation = Cooperation::find($request->session()->get('cooperation'));
            if ($cooperation instanceof Cooperation) {
                \Log::debug('Default cooperation -> '.$cooperation->id.' ('.$cooperation->slug.')');
                URL::defaults(['cooperation' => $cooperation->slug]);
            }
        }

        return $next($request);
    }
}
