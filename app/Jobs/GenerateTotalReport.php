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
use Illuminate\Support\Facades\App;

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
        if (App::runningInConsole()) {
            Log::debug(__CLASS__.' Is running in the console with a maximum execution time of: '.ini_get('max_execution_time'));
        }

        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $dumpService = DumpService::init()->anonymize($this->anonymizeData)
            ->inputSource($inputSource)
            ->createHeaderStructure();

        $cooperation = $this->cooperation;


        $rows[] = $dumpService->headerStructure;

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
                        'buildingVentilations' => fn ($q) => $q->forInputSource($inputSource),
                        'currentPaintworkStatus' => fn ($q) => $q->forInputSource($inputSource),
                        'heater' => fn ($q) => $q->forInputSource($inputSource),
                        'pvPanels' => fn ($q) => $q->forInputSource($inputSource),
                        'buildingServices' => fn ($q) => $q->forInputSource($inputSource),
                        'roofTypes' => fn ($q) => $q->forInputSource($inputSource),
                        'buildingElements' => fn ($q) => $q->forInputSource($inputSource),
                        'currentInsulatedGlazing' => fn ($q) => $q->forInputSource($inputSource),
                    ]
                );
            }, 'energyHabit' => fn ($q) => $q->forInputSource($inputSource)])
            ->chunkById(100, function($users) use ($dumpService, &$rows) {
                foreach ($users as $user) {
                    $rows[$user->building->id] = $dumpService->user($user)->generateDump();
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
        Log::debug($exception->getMessage() . ' ' . $exception->getTraceAsString());
        Log::debug("GenerateTotalReport failed: {$this->cooperation->id}");
        $this->fileStorage->delete();
    }
}
