<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CooperationHasScan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cooperation = $request->route('cooperation');
        $scan = $request->route('scan');

        if ($cooperation->scans()->where('scans.id', $scan->id)->doesntExist()) {
            return redirect()->route('cooperation.home');
        }

        return $next($request);
    }
}
