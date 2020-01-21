<?php

namespace App\Services;

use App\Models\Cooperation;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\User;

class FileStorageService
{

    /**
     * Method to download a given file.
     *
     * @param $fileStorage
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function download($fileStorage)
    {
        // when exist return the download
        if (\Storage::disk('downloads')->exists($fileStorage->filename)) {
            return \Storage::disk('downloads')->download($fileStorage->filename, $fileStorage->filename, [
                'Content-type'  => $fileStorage->fileType->content_type,
                'Pragma'        => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'       => '0',
            ]);
        }

        return redirect()->back()->with('warning', 'Er is iets fout gegaan');
    }
    /**
     * Check whether a file type is being processed for a given cooperation.
     *
     * @param FileType    $fileType
     * @param Cooperation $cooperation
     *
     * @return bool
     */
    public static function isFileTypeBeingProcessedForCooperation(FileType $fileType, Cooperation $cooperation): bool
    {
        return $fileType->whereHas('files', function ($q) {
            $q->beingProcessed();
        })->first() instanceof FileType;
    }

    /**
     * Check whether a file type is being processed for a given user and its input source.
     *
     * @param FileType    $fileType
     * @param User        $user
     * @param InputSource $inputSource
     *
     * @return bool
     */
    public static function isFileTypeBeingProcessedForUser(FileType $fileType, User $user, InputSource $inputSource): bool
    {
        return $fileType->whereHas('files', function ($q) use ($user, $inputSource) {
            $q->beingProcessed()->forMe($user)->forInputSource($inputSource);
        })->first() instanceof FileType;
    }
}
