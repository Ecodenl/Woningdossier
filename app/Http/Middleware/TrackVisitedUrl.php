<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use Closure;

class TrackVisitedUrl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isResident = HoomdossierSession::getInputSource(true)->short === InputSource::RESIDENT_SHORT;

        // we only want to track the last visited url for the resident
        if ($isResident && $request->isMethod('get') && !$request->ajax()) {
            $user = Hoomdossier::user();
            $user->update(['last_visited_url' => $request->fullUrl()]);
        }

        return $next($request);
    }
}
