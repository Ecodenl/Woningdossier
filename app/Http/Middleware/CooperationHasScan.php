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
        $cooperation = $request->route('cooperation');
        $scan = $request->route('scan');

        if ($cooperation->scans()->where('scans.id', $scan->id)->doesntExist()) {
            return redirect()->route('cooperation.home');
        }

        return $next($request);
    }
}
