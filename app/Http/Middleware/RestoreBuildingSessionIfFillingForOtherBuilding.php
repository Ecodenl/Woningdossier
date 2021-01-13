<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use Closure;
use Illuminate\Http\Request;

class RestoreBuildingSessionIfFillingForOtherBuilding
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // check if the user is filling a other building, and if so reset the building session id to its original user his building id.
        $user = Hoomdossier::user();

        if ($user->isFillingToolForOtherBuilding()) {
            HoomdossierSession::setBuilding($user->building);
        }

        return $next($request);
    }
}
