<?php

namespace App\Services\Econobis\Payloads;

use App\Models\FileType;
use App\Models\InputSource;
use Illuminate\Support\Facades\Storage;

class PdfReportPayload extends EconobisPayload
{
    public function buildPayload(): array
    {
        $building = $this->building;

        $fileType = FileType::findByShort('pdf-report');
        // there is no PDF for the master.
        $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        $fileStorage = $fileType
            ->files()
            ->forMyCooperation($building->user->cooperation_id)
            ->forBuilding($building)
            ->forInputSource($inputSource)
            ->withExpired()
            ->first();

        if (Storage::disk('downloads')->exists($fileStorage->filename)) {
            $file = Storage::disk('downloads')->get($fileStorage->filename);
            return [
                'pdf' => [
                    'contents' => base64_encode($file),
                ],
            ];
        }
        return  [];
    }
}