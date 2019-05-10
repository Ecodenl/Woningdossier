<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileStorageController extends Controller
{

    public function download(Cooperation $cooperation, FileStorage $fileType, $fileStorageFilename)
    {
        $cooperationId = $cooperation->id;

        dd($fileType);
        /** @var FileType $fileType */
        $fileType
            ->files()
            ->where('cooperation_id', $cooperationId)
            ->where('filename', $fileStorageFilename)
            ->first();

        dd($fileType);
    }
}
