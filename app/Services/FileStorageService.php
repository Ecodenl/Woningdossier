<?php

namespace App\Services;

use App\Helpers\Hoomdossier;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\User;

class FileStorageService {

    /**
     * Check whether a file type is being processed for a given user and its input source
     *
     * @param FileType $fileType
     * @param InputSource $inputSource
     * @param User $user
     */
    public static function isFileTypeBeingProcessedForUser(FileType $fileType, User $user = null, InputSource $inputSource = null)
    {

        if ($user instanceof User) {
            return $fileType->whereHas('files', function ($q) use ($user, $inputSource) {
                $q->beingProcessed()->forMe($user)->forInputSource($inputSource);
            })->first();
        }


        if (Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']) ) {
            return $fileType->whereHas('files', function ($q) {
                $q->beingProcessed();
            })->first() instanceof FileType;
        }



//        FileType::whereHas('files', function ($q) {
//            $q->withExpired()->beingProcessed();
//        })->where('id', $this->id)->first() instanceof FileType;

        return $fileType->isBeingProcessed();
    }
}