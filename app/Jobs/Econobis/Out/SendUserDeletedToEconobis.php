<?php

namespace App\Jobs\Econobis\Out;

use App\Models\Building;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserDeletedToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    public array $accountRelated;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accountRelated)
    {
        $this->accountRelated = $accountRelated;
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
                ->delete(
                    $econobisService
                        ->setAccountRelated($this->accountRelated)
                        ->getPayload()
                );
        });
    }
}