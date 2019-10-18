<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use Closure;

class RedirectIfIsFillingForOtherBuilding
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
        // this is the deny-if-filling group, but this particular one can only be used when comparing.
        if ('cooperation.my-account.import-center.set-compare-session' === $request->route()->getName()) {
            return $next($request);
        }

        if ($request->user()->isFillingToolForOtherBuilding()) {
            \Log::debug('Wow, user id '.Hoomdossier::user()->id.' tried to do something fishy!');

            return redirect()->route('cooperation.tool.general-data.index');
        }

        return $next($request);
    }
}
