<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageController extends Controller
{
    /**
     * Download method to retrieve a file from the storage.
     *
     * @param Cooperation $cooperation
     * @param FileType    $fileType
     * @param             $fileStorageFilename
     *
     * @return StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Cooperation $cooperation, FileType $fileType, $fileStorageFilename)
    {
        $fileStorage = $fileType
            ->files()
            ->where('filename', $fileStorageFilename)
            ->first();

        if ($fileStorage instanceof FileStorage) {
            if (\Storage::disk('downloads')->exists($fileStorageFilename)) {
                return \Storage::disk('downloads')->download($fileStorageFilename, $fileStorageFilename, [
                    'Content-type'  => $fileStorage->content_type,
                    'Pragma'        => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires'       => '0',
                ]);
            } else {
                return redirect()->back()->with('warning', 'Er is iets fout gegaan');
            }
        }

        return redirect()->back();
    }
}
