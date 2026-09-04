<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\SmartTwin\Mapping\MappingEntry;
use App\Services\SmartTwin\Mapping\MappingReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Writes a mapping report to a CSV, one file per stored response, tracked in FileStorage exactly
 * like the raw JSON it belongs to. Super admin material: see FileStoragePolicy, which refuses these
 * for everyone else.
 *
 * Deliberately dumb — it renders what the mapping decided and nothing more, so the file stays an
 * honest reflection of the run rather than a second opinion about it.
 */
class MappingReportStorage
{
    public const DISK = 'downloads';
    public const FILE_TYPE_SHORT = SmartTwinFileTypes::MAPPING_REPORT;

    private const RETENTION_DAYS = 30;

    private const HEADER = [
        'cooperation', 'building_id', 'postcode', 'huisnummer', 'dossier_id', 'flow', 'retrieved_at',
        'path', 'path_group', 'key', 'value', 'data_type', 'status', 'status_description', 'target', 'note',
    ];

    public function store(MappingReport $report): FileStorage
    {
        $building = $report->building;
        $inputSource = $report->eventType->inputSource();
        $fileType = FileType::findByShort(self::FILE_TYPE_SHORT);
        $filename = $this->filenameFor($building, $inputSource);

        Storage::disk(self::DISK)->put($filename, $this->render($report));

        Log::debug('SmartTwin mapping report stored', [
            'building_id' => $building->getKey(),
            'filename'    => $filename,
            'rows'        => count($report->entries()),
        ]);

        // Same latest-wins bookkeeping as the raw response: replaying the mapping overwrites the
        // report in place instead of leaving a trail of stale ones.
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

    private function render(MappingReport $report): string
    {
        $handle = fopen('php://temp', 'r+');

        // A UTF-8 BOM keeps accented values readable, and the sep line makes Excel use the
        // separator we chose no matter which locale it runs in — a comma file lands in one column
        // in Dutch Excel, a semicolon file does the same in an English one.
        fwrite($handle, "\u{FEFF}sep=;\n");
        fputcsv($handle, self::HEADER, ';');

        $context = $this->contextFor($report);

        foreach ($report->entries() as $entry) {
            fputcsv($handle, array_merge($context, $this->row($entry)), ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * The identifying columns. Constant within a file, repeated per row so the result stays a flat
     * table Excel can filter, and so a downloaded file still says which building it is about.
     *
     * @return array<int, string>
     */
    private function contextFor(MappingReport $report): array
    {
        $building = $report->building;

        return [
            (string) $building->user?->cooperation?->name,
            (string) $building->getKey(),
            $building->postal_code,
            $building->number . $building->extension,
            (string) BuildingSettingHelper::getSettingValue(
                $building,
                BuildingSettingHelper::SHORT_SMARTTWIN_DOSSIER_ID,
                '',
            ),
            $report->eventType->inputSource()->short,
            (string) $this->retrievedAt($report),
        ];
    }

    /**
     * When the response this report is about was fetched. Taken from the stored JSON rather than
     * from the clock, so a replay keeps pointing at the moment the data actually came in.
     */
    private function retrievedAt(MappingReport $report): string
    {
        $raw = FileStorage::withExpired()
            ->allInputSources()
            ->where('building_id', $report->building->getKey())
            ->where('input_source_id', $report->eventType->inputSource()->id)
            ->whereHas('fileType', fn ($query) => $query->where('short', AdviceResultStorage::FILE_TYPE_SHORT))
            ->first();

        return $raw?->updated_at?->format('Y-m-d H:i:s') ?? '';
    }

    /**
     * @return array<int, string>
     */
    private function row(MappingEntry $entry): array
    {
        return [
            $entry->leaf->path,
            $entry->leaf->pathGroup,
            $entry->leaf->key(),
            $entry->leaf->displayValue(),
            $entry->leaf->dataType(),
            $entry->status->value,
            $entry->status->description(),
            $entry->target ?? '',
            $entry->note ?? '',
        ];
    }

    private function filenameFor(Building $building, InputSource $inputSource): string
    {
        return sprintf('smarttwin/%d/%s-mapping.csv', $building->getKey(), $inputSource->short);
    }
}
