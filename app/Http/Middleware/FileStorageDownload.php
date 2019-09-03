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
        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator']) && ($fileStorage instanceof FileStorage && $fileStorage->cooperation->id == HoomdossierSession::getCooperation())) {
            return $next($request);
        }

        if ($fileStorage instanceof FileStorage) {

            $userIsResidentOrCoach = Hoomdossier::user()->hasRoleAndIsCurrentRole(['resident', 'coach']);
            $fileIsGeneratedByCurrentBuilding = $fileStorage->building_id == HoomdossierSession::getBuilding();
            $fileInputSourceIsCurrentInputSource = $fileStorage->input_source_id == HoomdossierSession::getInputSource();

            if ($userIsResidentOrCoach && $fileIsGeneratedByCurrentBuilding && $fileInputSourceIsCurrentInputSource) {
                return $next($request);
            }
        }

        return redirect()->back();
    }
}
