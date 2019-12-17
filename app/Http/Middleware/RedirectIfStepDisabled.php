<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Closure;
use Illuminate\Support\Facades\URL;

class RedirectIfStepDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $step)
    {
        /** @var Cooperation $cooperation */
        $cooperation = $request->route()->parameter('cooperation');

        // only when the step is active we allow the request to happen.
        if ($cooperation->getActiveOrderedSteps()->keyBy('short')->has($step)) {
            return $next($request);
        }

        return redirect()->back();
    }
}
