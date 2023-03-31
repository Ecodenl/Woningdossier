<?php

namespace App\Jobs\Econobis\Out;

use App\Models\Building;
use App\Services\Econobis\Api\Econobis;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Payloads\PdfReportPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPdfReportToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    public Building $building;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EconobisService $econobisService, Econobis $econobis)
    {
        $this->wrapCall(function () use ($econobis, $econobisService) {
            $econobis
                ->hoomdossier()
                ->pdf($econobisService->forBuilding($this->building)->getPayload(PdfReportPayload::class));
        });
    }
}
