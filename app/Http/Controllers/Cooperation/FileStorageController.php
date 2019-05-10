<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileStorageController extends Controller
{

    public function download(Cooperation $cooperation, FileType $fileType, $fileStorageFilename)
    {

        /** @var FileType $fileType */
        $file = $fileType
            ->files()
            ->where('filename', $fileStorageFilename)
            ->first();

        dd($file);
    }
}
