<?php

namespace App\Services;

use Illuminate\Http\RedirectResponse;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    /**
     * method to delete a file storage, and the file on the disk.
     *
     * @throws \Exception
     */
    public static function delete(FileStorage $fileStorage)
    {
        $fileStorage->delete();
        Storage::disk('downloads')->delete($fileStorage->filename);
    }

    /**
     * Method to download a given file.
     */
    public static function download(FileStorage $fileStorage): StreamedResponse|RedirectResponse
    {
        // when exist return the download
        if (Storage::disk('downloads')->exists($fileStorage->filename)) {
            return Storage::disk('downloads')->download($fileStorage->filename, $fileStorage->filename, [
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
     */
    public static function isFileTypeBeingProcessedForCooperation(FileType $fileType, Cooperation $cooperation): bool
    {
        return $fileType->whereHas('files', function ($q) {
            $q->beingProcessed();
        })->first() instanceof FileType;
    }

    /**
     * Check whether a file type is being processed for a given user and its input source.
     */
    public static function isFileTypeBeingProcessedForUser(FileType $fileType, User $user, InputSource $inputSource): bool
    {
        return $fileType->whereHas('files', function ($q) use ($user, $inputSource) {
            $q->withExpired()->beingProcessed()->forMe($user)->forInputSource($inputSource);
        })->first() instanceof FileType;
    }
}
