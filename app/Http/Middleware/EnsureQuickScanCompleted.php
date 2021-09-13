<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Step;
use App\Models\SubStep;
use Closure;

class EnsureQuickScanCompleted
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
        $building = HoomdossierSession::getBuilding(true);

        if ($building instanceof Building) {
            if ($building->hasCompletedQuickScan()) {
                return $next($request);
            } else {
                $firstIncompleteStep = $building->getFirstIncompleteStep();

                if ($firstIncompleteStep instanceof Step) {
                    $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep);

                    if ($firstIncompleteSubStep instanceof SubStep) {
                        return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
                            'step' => $firstIncompleteStep,
                            'subStep' => $firstIncompleteSubStep,
                        ]);
                    }
                }
            }
        }

        // Either the building is not relevant, or something funky has happened with the completed quick scan
        // steps. Either way, we cannot let the building go through to the expert tool. We redirect back home
        return redirect()->route('cooperation.home');
    }
}
