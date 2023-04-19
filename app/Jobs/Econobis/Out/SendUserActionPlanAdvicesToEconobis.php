<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Queue;
use App\Models\Building;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use App\Services\Econobis\Payloads\WoonplanPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserActionPlanAdvicesToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    public Building $building;

    public $queue = Queue::APP_EXTERNAL;

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
        $this->wrapCall(function () use ($econobis, $econobisService) {
            $econobis
                ->forCooperation($this->building->user->cooperation)
                ->hoomdossier()
                ->woonplan($econobisService->forBuilding($this->building)->getPayload(WoonplanPayload::class));
        });
    }
}
