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
            return $next($request);
        }

        if ($cooperation->scans()->where('scans.id', $scan->id)->doesntExist()) {
            return to_route('cooperation.home');
        }

        return $next($request);
    }
}
