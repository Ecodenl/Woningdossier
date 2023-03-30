<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendPdfReportToEconobis;
use App\Jobs\Econobis\Out\SendUserActionPlanAdvicesToEconobis;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Integration;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Api\Econobis;
use App\Services\Econobis\Payloads\BuildingStatusPayload;
use App\Services\Econobis\Payloads\PdfReportPayload;
use App\Services\FileTypeService;
use App\Services\IntegrationProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PdfReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:pdf-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the "woonplan" (user action plan advices) to Econobis, will take all users that have changed their tool in the last configured interval.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(IntegrationProcessService $integrationProcessService)
    {
        $integrationProcessService = $integrationProcessService
            ->forIntegration(Integration::findByShort('econobis'))
            ->forProcess(SendPdfReportToEconobis::class);

        // first get all file storages that have been updated in the past 30 minutes
        // than check if the user his advices werent synced in the past 30 minutes
        $interval = Carbon::now()->subMinutes(config("hoomdossier.services.econobis.interval.".SendPdfReportToEconobis::class));

        FileStorage::where('updated_at', '>=', $interval)
            // we query on the coach, the payload itself only includes the coach
            // so makes sense to do it here aswell
            ->forInputSource(InputSource::coach())
            ->where('file_type_id', FileType::findByShort('pdf-report')->id)
            ->forAllCooperations()
            ->chunkById(50, function ($fileStorages) use ($integrationProcessService, $interval) {
                foreach ($fileStorages as $fileStorage) {
                    $lastSyncedAt = $integrationProcessService->forBuilding($fileStorage->building)->lastSyncedAt();

                    $shouldSync = false;
                    if (is_null($lastSyncedAt)) {
                        $shouldSync = true;
                    } elseif ($fileStorage->updated_at->greaterThan($lastSyncedAt)) {
                        $shouldSync = true;
                    }

                    dd($shouldSync);

                    if ($shouldSync) {
                        SendPdfReportToEconobis::dispatch($fileStorage->building);
                    }
                }
            });

        return 0;
    }
}
