<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Services\WoonplanService;
use Closure;
use Illuminate\Support\Facades\Route;

class EnsureQuickScanCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $building = HoomdossierSession::getBuilding(true);

        if (Route::currentRouteName() === 'cooperation.tool.questionnaire.store') {
            // A questionnaire is the only exception on this rule
            return $next($request);
        }

        if ($building instanceof Building) {
            $scan = Scan::findByShort('quick-scan');
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            $woonplanService = WoonplanService::init($building)->scan($scan);
            if ($woonplanService->canEnterExpertScan($request->route('cooperation'))) {
                return $next($request);
            } else {
                $firstIncompleteStep = $building->getFirstIncompleteStep($scan, $masterInputSource);

                if ($firstIncompleteStep instanceof Step) {
                    $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, $masterInputSource);

                    if ($firstIncompleteSubStep instanceof SubStep) {
                        return to_route('cooperation.frontend.tool.simple-scan.index', [
                            // so this may need some rework to check from a scan standpoint.
                            'scan' => $scan,
                            'step' => $firstIncompleteStep,
                            'subStep' => $firstIncompleteSubStep,
                        ]);
                    }
                }
            }
        }


        // Either the building is not relevant, or something funky has happened with the completed quick scan
        // steps. Either way, we cannot let the building go through to the expert tool. We redirect back home
        return to_route('cooperation.home');
    }
}
