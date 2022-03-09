<?php

namespace App\Services;

use App\Models\FileType;
use Illuminate\Support\Str;

class FileTypeService {

    public FileType $fileType;

    public function __construct(FileType $fileType)
    {
        $this->fileType = $fileType;
    }

    public function niceFileName(): string
    {
        // create a short hash to prepend on the filename.
        $substrBycrypted = substr(\Hash::make(Str::uuid()), 7, 5);
        $substrUuid = substr(Str::uuid(), 0, 8);
        $hash = $substrUuid.$substrBycrypted;

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
        // remove the / to prevent unwanted directories
        return str_replace('/', '', $hash.Str::slug($this->fileType->name).'.csv');
    }
}