<?php

namespace App\Jobs;

use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\ContentStructureService;
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

        $dumpService->setHeaderStructure(
            ContentStructureService::init($dumpService->headerStructure)->applicableForTotalReport()
        );

        $cooperation = $this->cooperation;

        $rows[] = $dumpService->headerStructure;
        $chunkNo = 1;

        // Get all users with a building and who have completed the quick scan
        $cooperation->users()
            ->whereHas('building.buildingStatuses')
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
            ->chunkById(100, function($users) use ($dumpService, &$rows, &$chunkNo) {
                foreach ($users as $user) {
                    $rows[$user->building->id] = $dumpService->user($user)->generateDump();
                }

                Log::debug(__METHOD__ . ' Putting chunk ' . $chunkNo);
                $path = Storage::disk('downloads')->path($this->fileStorage->filename);
                $handle = fopen($path, 'a');
                if (!$handle){
                    Log::error(__METHOD__ . " no handle");
                }
                Log::debug(__METHOD__ . ' ' . count($rows) .  ' rows');
                foreach ($rows as $row) {
                    $lines = fputcsv($handle, $row);
                    if ($lines === false){
                        Log::error(__METHOD__ . " no lines written to path " . $path);
                    }
                    else {
                        Log::error(__METHOD__ . " " . $lines . " lines written to path " . $path);
                    }
                }
                Log::debug(__METHOD__ . ' closing handle');
                fclose($handle);
                Log::debug(__METHOD__ . ' Chunk ' . $chunkNo . ' put');
                $chunkNo++;

                // empty the rows, to prevent it from becoming to big and potentially slow.
                $rows = [];
            });

        $this->fileStorage->isProcessed();
    }

    public function Failed(\Throwable $exception)
    {
        Log::debug($exception->getMessage() . ' ' . $exception->getTraceAsString());
        Log::debug("GenerateTotalReport failed: {$this->cooperation->id}");
        $this->fileStorage->delete();
    }
}
