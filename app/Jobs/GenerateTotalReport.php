<?php

namespace App\Jobs;

use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\DumpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateTotalReport implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;

    public function __construct(Cooperation $cooperation, FileType $fileType, FileStorage $fileStorage, bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->cooperation = $cooperation;
        $this->anonymizeData = $anonymizeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (\App::runningInConsole()) {
            \Log::debug(__CLASS__.' Is running in the console with a maximum execution time of: '.ini_get('max_execution_time'));
        }

        $anonymized = $this->anonymizeData;
        $cooperation = $this->cooperation;

        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $headers = DumpService::getStructureForTotalDumpService($anonymized);

        $rows[] = $headers;

        // Get all users with a building and who have completed the quick scan
        $cooperation->users()
            ->whereHas('building')
            ->with(['building' => function ($query) use ($inputSource) {
                $query->with(
                    [
                        'buildingFeatures' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource)
                                ->with([
                                    'roofType', 'energyLabel', 'damagedPaintwork', 'plasteredSurface',
                                    'contaminatedWallJoints', 'wallJoints',
                                ]);
                        },
                        'buildingVentilations' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'currentPaintworkStatus' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'heater' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'pvPanels' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'buildingServices' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'roofTypes' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'buildingElements' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'currentInsulatedGlazing' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                    ]
                );
            }, 'energyHabit' => function ($query) use ($inputSource) {
                $query->forInputSource($inputSource);
            }])
            ->chunkById(100, function($users) use ($headers, $cooperation, $inputSource, $anonymized, &$rows) {
                foreach ($users as $user) {
                    $rows[$user->building->id] = DumpService::totalDump($headers, $cooperation, $user, $inputSource, $anonymized, false)['user-data'];
                }

                $handle = fopen(Storage::disk('downloads')->path($this->fileStorage->filename), 'a');
                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);

                // empty the rows, to prevent it from becoming to big and potentially slow.
                $rows = [];
            });


        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }
}
