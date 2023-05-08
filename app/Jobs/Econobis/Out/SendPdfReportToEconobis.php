<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Queue;
use App\Jobs\Middleware\EnsureCooperationHasEconobisLink;
use App\Models\Building;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Payloads\PdfReportPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPdfReportToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    public $queue = Queue::APP_EXTERNAL;

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
    public function handle(EconobisService $econobisService, EconobisApi $econobis)
    {
        Log::debug("Processing PDF report payload to Econobis for building {$this->building->id}");
        $this->wrapCall(function () use ($econobis, $econobisService) {
            $econobis
                ->forCooperation($this->building->user->cooperation)
                ->hoomdossier()
                ->pdf($econobisService->forBuilding($this->building)->getPayload(PdfReportPayload::class));
        });
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [
            new EnsureCooperationHasEconobisLink($this->building->user->cooperation),
            (new WithoutOverlapping(sprintf('%s-%s', __CLASS__, $this->building->id)))->dontRelease()
        ];
    }
}
