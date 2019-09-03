<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\FileStorage;
use Closure;

class FileStorageDownload
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
        $routeParameters = $request->route()->parameters();
        $fileType = $routeParameters['fileType'];
        $fileStorageFilename = $routeParameters['fileStorageFilename'];

        $fileStorage = $fileType->files()->where('filename', $fileStorageFilename)->first();

        // some other logic for resident wil come in the near future.
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator'])) {

            if ($fileStorage instanceof FileStorage && $fileStorage->cooperation->id == HoomdossierSession::getCooperation()) {
                return $next($request);
            }
        }

        if ((Hoomdossier::user()->hasRoleAndIsCurrentRole(['resident', 'coach']) && $fileStorage->building_id == HoomdossierSession::getBuilding()) && $fileStorage->input_source_id == HoomdossierSession::getInputSource()) {
            return $next($request);
        }

        return redirect()->back();
    }
}
