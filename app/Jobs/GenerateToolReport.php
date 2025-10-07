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
use App\Traits\ShouldLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Throwable;

class GenerateToolReport implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels, ShouldLog;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;
    protected string $path;

    public $tries = 1;

    public function __construct(Cooperation $cooperation, FileType $fileType, FileStorage $fileStorage, bool $anonymizeData = false)
    {
        $this->queue = Queue::EXPORTS;
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->cooperation = $cooperation;
        $this->anonymizeData = $anonymizeData;

        $this->path = Storage::disk('downloads')->path($fileStorage->filename);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Silence all events dispatched because otherwise it clogs the memory with events (and causes overflow)
        // that we don't even care about.
        DB::unsetEventDispatcher();
        $this->log(__CLASS__  . " generating {$this->fileType->short} for cooperation {$this->cooperation->id}.");

        if (App::runningInConsole()) {
            $this->log(__CLASS__ . ' Is running in the console with a maximum execution time of: ' . ini_get('max_execution_time'));
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
            $dumpService->headerStructure[] = 'Contact id';
        }

        $handle = fopen($this->path, 'a');
        fputcsv($handle, $dumpService->headerStructure);
        
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
            ->chunkById(100, function ($users) use ($dumpService, $handle) {
                $this->log(__CLASS__ . ' (startOfChunk) - ' . memory_get_usage());

                foreach ($users as $user) {
                    $this->log(__CLASS__ . ' (pre-gen) - ' . memory_get_usage());
                    $dataToWrite = $dumpService->user($user)->generateDump();
                    $this->log(__CLASS__ . ' (post-gen) - ' . memory_get_usage());

                    if ($this->fileType->short === 'total-report') {
                        $dataToWrite['Account id'] = $user->account_id;
                        $dataToWrite['User id'] = $user->id;
                        $dataToWrite['Building id'] = $user->building->id;
                        $dataToWrite['Contact id'] = optional($user->extra)['contact_id'];
                    }

                    fputcsv($handle, $dataToWrite);
                }

                $this->log(__CLASS__ . ' (endOfChunk) - ' . memory_get_usage());

                unset($users);
                gc_collect_cycles();
            });
        fclose($handle);

        $this->fileStorage->finishProcess();
    }

    public function failed(Throwable $exception)
    {
        $this->fileStorage->delete();

        $this->log("GenerateTotalReport failed: {$this->cooperation->id}");
        report($exception);
    }
}
