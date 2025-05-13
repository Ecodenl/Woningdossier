<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function serve(Cooperation $cooperation, Media $media): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->processMedia($cooperation, $media, 'file');
    }

    public function download(Cooperation $cooperation, Media $media): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->processMedia($cooperation, $media, 'download');
    }

    protected function processMedia(Cooperation $cooperation, Media $media, string $method): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $currentInputSource = Auth::check() ? HoomdossierSession::getInputSource(true) : null;

        $this->authorize('view', [$media, $currentInputSource]);

        return response()->{$method}(
            Storage::disk($media->disk)->path($media->getDiskPath())
        );
    }
}