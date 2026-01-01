<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class CooperationHasScan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $request->route('cooperation');
        /** @var \App\Models\Scan $scan */
        $scan = $request->route('scan');

        if ($cooperation->scans()->where('scans.id', $scan->id)->doesntExist()) {
            return to_route('cooperation.home');
        }

        return $next($request);
    }
}
