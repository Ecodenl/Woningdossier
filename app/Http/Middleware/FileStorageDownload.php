<?php

namespace App\Http\Middleware;

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


        // some other logic for resident wil come in the near future.
        if (\Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator'])) {
            $fileStorage = $fileType->files()->where('filename', $fileStorageFilename)->first();

            if ($fileStorage instanceof FileStorage && $fileStorage->cooperation->id == HoomdossierSession::getCooperation()) {
                return $next($request);
            }
        }

        return redirect()->back();
    }
}
