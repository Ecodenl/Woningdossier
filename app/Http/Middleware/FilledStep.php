<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use App\Models\Step;
use Closure;

class FilledStep
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
        $step = Step::whereSlug($step)->first();
        $debugMsg = 'For this step, the '.$step->slug.' should be filled';

        if (! \Auth::user()->hasCompleted($step)) {
            \Log::debug($debugMsg.".. And it wasn't. So, redirecting to that step..");
            $cooperation = Cooperation::find($request->session()->get('cooperation'));

            return redirect('/tool/'.$step->slug.'/')->with(compact('cooperation'));
        }
        \Log::debug($debugMsg.'.. And it was :-)');

        return $next($request);
    }
}
