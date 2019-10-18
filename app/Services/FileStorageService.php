<?php

namespace App\Services;

use App\Models\Cooperation;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\User;

class FileStorageService
{
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
