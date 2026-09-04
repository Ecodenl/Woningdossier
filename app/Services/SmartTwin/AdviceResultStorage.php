<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use Illuminate\Support\Facades\Storage;

class AdviceResultStorage
{
    public const DISK = 'downloads';
    public const FILE_TYPE_SHORT = SmartTwinFileTypes::ADVICE_RAW;

    private const RETENTION_DAYS = 30;

    /**
     * Persist the raw advice/quickscan response as a JSON file, tracked by a single
     * FileStorage row per building + flow (latest-wins). The mapping (phase 2) reads
     * from this artifact, so a mapping retry never has to re-hit the SmartTwin API.
     *
     * @param  array<string, mixed>  $results
     */
    public function storeRaw(Building $building, EventType $eventType, array $results): FileStorage
    {
        $inputSource = $eventType->inputSource();
        $fileType = FileType::findByShort(self::FILE_TYPE_SHORT);
        $filename = $this->filenameFor($building, $inputSource);

        Storage::disk(self::DISK)->put(
            $filename,
            json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );

        // withExpired()->allInputSources() bypasses the AvailableScope and the input-source
        // scope, so the lookup reliably finds any existing row and overwrites it in place
        // instead of creating a duplicate.
        return FileStorage::withExpired()
            ->allInputSources()
            ->updateOrCreate(
                [
                    'building_id'     => $building->getKey(),
                    'input_source_id' => $inputSource->id,
                    'file_type_id'    => $fileType->id,
                ],
                [
                    'cooperation_id'     => $building->user?->cooperation?->id,
                    'filename'           => $filename,
                    'is_being_processed' => false,
                    'available_until'    => now()->addDays(self::RETENTION_DAYS),
                ],
            );
    }

    /**
     * Read back the raw response stored for a building + flow. Null when nothing was stored yet,
     * or when the file was cleaned up (the row expires after RETENTION_DAYS).
     *
     * @return null|array<string, mixed>
     */
    public function readRaw(Building $building, EventType $eventType): ?array
    {
        $filename = $this->filenameFor($building, $eventType->inputSource());

        if (! Storage::disk(self::DISK)->exists($filename)) {
            return null;
        }

        return json_decode(Storage::disk(self::DISK)->get($filename), true);
    }

    private function filenameFor(Building $building, InputSource $inputSource): string
    {
        return sprintf('smarttwin/%d/%s-advice.json', $building->getKey(), $inputSource->short);
    }
}
