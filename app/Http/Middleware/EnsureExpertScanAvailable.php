<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Scan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Closes the expert scan once the building's woonplan has gone over to SmartTwin.
 *
 * From the switch on (Hoomdossier::smartTwinOwnsWoonplanFor()) SmartTwin describes the dwelling and
 * makes the advices, so the expert scan has nothing left to add, and the answers it would save are
 * overwritten by the next import. Before the switch it stays open, also in a cooperation that took
 * SmartTwin on: a finished dossier that never goes through SmartTwin keeps working as it did.
 *
 * Guards both ways in: the expert scan itself and the old tool controllers, which calculate advices
 * of their own.
 */
class EnsureExpertScanAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        // Questionnaires post here from the simple scans as well.
        if (Route::currentRouteName() === 'cooperation.tool.questionnaire.store') {
            return $next($request);
        }

        $building = HoomdossierSession::getBuilding(true);

        if ($building instanceof Building && Hoomdossier::smartTwinOwnsWoonplanFor($building)) {
            return to_route('cooperation.frontend.tool.simple-scan.my-plan.index', [
                'cooperation' => $request->route('cooperation'),
                'scan'        => Scan::quick(),
            ]);
        }

        return $next($request);
    }
}
