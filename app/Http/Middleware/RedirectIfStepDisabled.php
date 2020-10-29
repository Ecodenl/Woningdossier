<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use Closure;

class RedirectIfStepDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
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
