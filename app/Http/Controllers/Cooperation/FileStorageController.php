<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileStorageController extends Controller
{

    /**
     * Download method to retrieve a file from the storage
     *
     * @param  Cooperation  $cooperation
     * @param  FileType     $fileType
     * @param               $fileStorageFilename
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Cooperation $cooperation, FileType $fileType, $fileStorageFilename)
    {
        $fileStorage = $fileType
            ->files()
            ->where('filename', $fileStorageFilename)
            ->first();

        if ($fileStorage instanceof FileStorage) {

            if (\Storage::disk('downloads')->exists($fileStorageFilename, $fileType->name.'.csv')) {

                return \Storage::disk('downloads')->download($fileStorageFilename, $fileType->name.'.csv', [
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
