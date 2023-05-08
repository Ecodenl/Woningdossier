<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Helpers\ToolHelper;
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
use Throwable;

class GenerateToolReport implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $queue = Queue::EXPORTS;

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

        // Define dump type based on file type
        $short = ToolHelper::STRUCT_TOTAL;
        switch ($this->fileType->short) {
            case 'total-report':
            case 'total-report-anonymized':
                $short = ToolHelper::STRUCT_TOTAL;
                break;
            case 'lite-scan-report':
            case 'lite-scan-report-anonymized':
                $short = ToolHelper::STRUCT_LITE;
                break;
            case 'small-measures-report':
            case 'small-measures-report-anonymized':
                $short = ToolHelper::STRUCT_SMALL_MEASURES_LITE;
                break;
        }

        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $dumpService = DumpService::init()
            ->anonymize($this->anonymizeData)
            ->inputSource($inputSource)
            ->setMode(DumpService::MODE_CSV)
            ->createHeaderStructure($short);

        $dumpService->setHeaderStructure(
            ContentStructureService::init($dumpService->headerStructure)->applicableForCsvReport()
        );

        $cooperation = $this->cooperation;

        // note: not ideal, However doing this in the correct place takes time.
        if ($this->fileType->short === 'total-report') {
            $dumpService->headerStructure[] = 'Account id';
            $dumpService->headerStructure[] = 'User id';
            $dumpService->headerStructure[] = 'Building id';
        }

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
                    if ($this->fileType->short === 'total-report') {
                        $rows[$user->building->id]['Account id'] = $user->account_id;
                        $rows[$user->building->id]['User id'] = $user->id;
                        $rows[$user->building->id]['Building id'] = $user->building->id;
                    }
                }

                Log::debug('GenerateTotalReport - Putting chunk ' . $chunkNo);
                $path = Storage::disk('downloads')->path($this->fileStorage->filename);
                $handle = fopen($path, 'a');
                if (!$handle){
                    Log::error('GenerateTotalReport - no handle');
                }
                Log::debug('GenerateTotalReport - ' . count($rows) .  ' rows on chunk ' . $chunkNo);
                foreach ($rows as $row) {
                    $strlen = fputcsv($handle, $row);
                    if ($strlen === false){
                        Log::error('GenerateTotalReport - no characters written to path ' . $path);
                    }
                    else {
                        Log::error('GenerateTotalReport - ' . $strlen . " characters written to path " . $path);
                    }
                }
                Log::debug('GenerateTotalReport - closing handle');
                fclose($handle);
                Log::debug('GenerateTotalReport - Chunk ' . $chunkNo . ' put');
                $chunkNo++;

                // empty the rows, to prevent it from becoming to big and potentially slow.
                $rows = [];
            });

        $this->fileStorage->isProcessed();
    }

    public function failed(Throwable $exception)
    {
        $this->fileStorage->delete();

        Log::debug("GenerateTotalReport failed: {$this->cooperation->id}");
        report($exception);
    }
}
