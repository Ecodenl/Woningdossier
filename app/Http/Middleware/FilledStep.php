<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
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
    public function handle($request, Closure $next, $stepSlug)
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $step = Step::whereSlug($stepSlug)->first();

        $debugMsg = 'For this step, the '.$stepSlug.' should be filled';
        $prev = Step::where('order', $step->order)->first();

        if (! $building->complete($prev)) {
            \Log::debug($debugMsg.".. And it wasn't. So, redirecting to that step..");
            $cooperation = Cooperation::find($request->session()->get('cooperation'));

            return redirect('/tool/'.$prev->slug.'/')->with(compact('cooperation'));
        }
        \Log::debug($debugMsg.'.. And it was :-)');

        return $next($request);
    }
}
