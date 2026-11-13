<?php

namespace App\Jobs\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Helpers\MediaHelper;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Scan;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\UserReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Plank\Mediable\Facades\MediaUploader;
use Throwable;

/**
 * Makes the woonplan report when a coach releases a SmartTwin woonplan (the building status goes to
 * "Uitgevoerd"), and keeps it (decision 2026-10-16-smarttwin-rapport-bij-vrijgeven):
 *
 * - With the building's files, tagged "Rapportage", shared with the cooperation and uploaded as the
 *   coach, so resident, coach and cooperation see it. A new file every time, the date in its name:
 *   a second round keeps the report of the first.
 * - As the coach's report, in place of the last one the coach made with the button, so Econobis
 *   gets it the way it gets that one (api:econobis:out:hoomdossier:pdf-report).
 *
 * Only for a woonplan that is SmartTwin's: releasing is SmartTwin's. Queued, so releasing does not
 * wait for it; if it fails, the woonplan is released all the same and the failure is reported.
 */
class StoreReleasedWoonplanReport implements ShouldQueue
{
    use Queueable;

    /** What marks the file as made on release, in its custom properties. */
    public const MADE_ON_RELEASE = 'made_on_release';

    public $tries = 3;

    public $backoff = 60;

    public function __construct(public Building $building)
    {
        $this->onQueue(Queue::EXPORTS);
    }

    public function handle(UserReport $userReport): void
    {
        $building = $this->building;
        $user = $building->user()->forAllCooperations()->first();

        // Checked again: the cooperation may have switched SmartTwin off since.
        if (! $user instanceof User || ! Hoomdossier::smartTwinOwnsWoonplanFor($building)) {
            return;
        }

        $pdf = $userReport->render($user, Scan::quick());
        $date = Carbon::now();

        $this->storeWithTheFiles($building, $pdf, $date);
        $this->storeAsTheCoachsReport($building, $user, $pdf);
    }

    public function failed(Throwable $exception): void
    {
        report($exception);
    }

    private function storeWithTheFiles(Building $building, string $pdf, Carbon $date): Media
    {
        $path = tempnam(sys_get_temp_dir(), 'woonplan-report') . '.pdf';
        file_put_contents($path, $pdf);

        try {
            /** @var Media $media */
            $media = MediaUploader::fromSource($path)
                ->toDestination('uploads', "buildings/{$building->id}")
                ->useFilename('woonplan-rapportage-' . $date->format('d-m-Y'))
                ->beforeSave(function (Media $media) use ($date) {
                    $media->custom_properties = [
                        'share_with_cooperation' => true,
                        self::MADE_ON_RELEASE    => $date->toDateString(),
                    ];
                    $media->input_source_id = InputSource::coach()->id;
                })
                ->upload();
        } finally {
            @unlink($path);
        }

        $building->attachMedia($media, MediaHelper::REPORT);

        return $media;
    }

    /**
     * As the button under the woonplan does for the coach: the coach's previous report goes, this one
     * takes its place.
     */
    private function storeAsTheCoachsReport(Building $building, User $user, string $pdf): FileStorage
    {
        $fileType = FileType::findByShort('pdf-report');
        $coach = InputSource::coach();

        $fileType->files()
            ->withExpired()
            ->forBuilding($building)
            ->forInputSource($coach)
            ->get()
            ->each(fn (FileStorage $fileStorage) => FileStorageService::delete($fileStorage));

        $fileStorage = FileStorage::create([
            'building_id'     => $building->id,
            'cooperation_id'  => $user->cooperation_id,
            'input_source_id' => $coach->id,
            'file_type_id'    => $fileType->id,
            // As DownloadPdf names it.
            'filename'        => trim($building->postal_code) . $building->number . '-' . Str::slug($user->getFullName()) . '-' . $coach->name . '.pdf',
        ]);

        Storage::disk('downloads')->put($fileStorage->filename, $pdf);
        $fileStorage->finishProcess();

        return $fileStorage;
    }
}
