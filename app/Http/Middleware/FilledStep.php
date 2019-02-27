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
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $stepSlug)
    {
        // get the building from the user
        $building = Building::find(HoomdossierSession::getBuilding());
        if ($building instanceof Building) {
            // get the current step
            $step = Step::whereSlug($stepSlug)->first();
            $debugMsg = 'For this step, the ' . $stepSlug . ' should be filled';

            // if the user / building did not complete the given step redirect him back.
            if ($building->hasNotCompleted($step)) {
                \Log::debug($debugMsg . ".. And it wasn't. So, redirecting to that step..");
                $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

                return redirect('/tool/' . $stepSlug . '/')->with(compact('cooperation'));
            }
            \Log::debug($debugMsg . '.. And it was :-)');
        } else {
            $buildingDebugMsg = 'The $building is not an instance,';
            $buildingWithTrashed = Building::withTrashed()->find(HoomdossierSession::getBuilding());
            if ($buildingWithTrashed instanceof Building) {
                $buildingDebugMsg .= 'the building has been thrashed.';
            } else {
                $buildingDebugMsg .= 'The building is not trashed so the building session is empty for user_id: '.\Auth::id();
            }
            \Log::debug($buildingDebugMsg);


            // since the building is not set, we logout the user otherwise it would lead to weird behaviour
            HoomdossierSession::destroy();
            \Auth::logout();
            $request->session()->invalidate();

            return redirect(url('/login'))->with('warning', __('auth.session-invalid'));
        }

        return $next($request);
    }
}
