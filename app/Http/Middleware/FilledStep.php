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
        // get the building from the user
        $building = Building::find(HoomdossierSession::getBuilding());
        // get the current step
        $step = Step::whereSlug($stepSlug)->first();
        $debugMsg = 'For this step, the '.$stepSlug.' should be filled';

        // if the user / building did not complete the given step redirect him back.
        if ($building->hasNotCompleted($step)) {
            \Log::debug($debugMsg.".. And it wasn't. So, redirecting to that step..");
            $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

            return redirect('/tool/'.$stepSlug.'/')->with(compact('cooperation'));
        }
        \Log::debug($debugMsg.'.. And it was :-)');

        return $next($request);
    }
}
