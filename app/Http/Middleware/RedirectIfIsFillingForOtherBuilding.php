<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfIsFillingForOtherBuilding
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
        if ($request->user()->isFillingToolForOtherBuilding()) {
            \Log::debug('Wow, user id '.\Auth::id().' tried to do something fishy!');
            return redirect()->route('cooperation.tool.general-data.index');
        }
        return $next($request);
    }
}
