<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use Closure;
use Illuminate\Support\Facades\Route;

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

        if (Route::currentRouteName() === 'cooperation.tool.questionnaire.store') {
            // A questionnaire is the only exception on this rule
            return $next($request);
        }


        if ($building instanceof Building) {
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            if ($building->hasCompletedQuickScan($masterInputSource)) {
                return $next($request);
            } else {
                $firstIncompleteStep = $building->getFirstIncompleteStep([], $masterInputSource);

                if ($firstIncompleteStep instanceof Step) {
                    $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, [], $masterInputSource);

                    if ($firstIncompleteSubStep instanceof SubStep) {

                        return redirect()->route('cooperation.frontend.tool.simple-scan.index', [
                            // so this may need some rework to check from a scan standpoint.
                            'scan' => $firstIncompleteStep->scan,
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
