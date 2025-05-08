<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function serve(Cooperation $cooperation, Media $media)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);

        $this->authorize('view', [$media, $currentInputSource]);

        return response()->file(
            Storage::disk($media->disk)->path($media->getDiskPath())
        );
    }

    public function download(Cooperation $cooperation, Media $media)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);

        $this->authorize('view', [$media, $currentInputSource]);

        return response()->download(
            Storage::disk($media->disk)->path($media->getDiskPath())
        );
    }
}