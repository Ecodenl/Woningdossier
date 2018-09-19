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
        \Log::debug('For this step, the '.$step.' should be filled');
        $prev = Step::where('order', $step)->first();
        if (! \Auth::user()->hasCompleted($prev)) {
            \Log::debug("And it wasn't. So, redirecting to that step..");
            $cooperation = Cooperation::find($request->session()->get('cooperation'));

            return redirect('/tool/'.$prev->slug.'/')->with(compact('cooperation'));
        }
        \Log::debug('And it was :-)');

        return $next($request);
    }
}
