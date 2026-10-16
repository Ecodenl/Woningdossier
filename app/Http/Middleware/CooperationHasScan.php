<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Helpers\HoomdossierSession;
use App\Helpers\ScanAvailabilityHelper;
use App\Models\Building;
use Closure;
use Illuminate\Http\Request;

class CooperationHasScan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $request->route('cooperation');
        /** @var \App\Models\Scan $scan */
        $scan = $request->route('scan');

        $building = HoomdossierSession::getBuilding(true);

        if ($building instanceof Building && ScanAvailabilityHelper::isAvailableForBuilding($building, $scan)) {
            // Every page of a simple scan passes here, which makes it the place to note which of the
            // two the user is in. The navigation reads it back on pages that carry no scan. Not the
            // shared files: they belong to the building, and only hang off a scan for their route.
            // The navigation links them through whichever scan comes first, so opening them before
            // choosing on the dashboard would otherwise make that choice for the user.
            if (! $request->routeIs('cooperation.frontend.tool.simple-scan.my-plan.media')) {
                HoomdossierSession::setScan($scan);
            }

            return $next($request);
        }

        if ($cooperation->scans()->where('scans.id', $scan->id)->doesntExist()) {
            return to_route('cooperation.home');
        }

        return $next($request);
    }
}
