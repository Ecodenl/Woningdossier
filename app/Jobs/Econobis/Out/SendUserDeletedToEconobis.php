<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Wrapper;
use App\Jobs\Middleware\EnsureCooperationHasEconobisLink;
use App\Helpers\Queue;
use App\Models\Cooperation;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Predis\Response\ServerException;

class SendUserDeletedToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected array $cooperation, protected array $accountRelated)
    {
        $this->onQueue(Queue::APP_EXTERNAL);
    }

    /**
     * Execute the job.
     */
    public function handle(EconobisService $econobisService, EconobisApi $econobis): void
    {
        $this->wrapCall(function () use ($econobis, $econobisService) {
            $econobis
                ->forCooperation(new Cooperation($this->cooperation)) // Hydrate a new cooperation model for ease.
                ->hoomdossier()
                ->delete(
                    $econobisService
                        ->setAccountRelated($this->accountRelated)
                        ->getPayload()
                );
        });
    }

    public function wrapCall(\Closure $function): void
    {
        if (Hoomdossier::hasEnabledEconobisCalls()) {
            Wrapper::wrapCall(
                function () use ($function) {
                    $function();
                    // normally, in the trait we would set the synced at time
                    // but the user is deleted, so we cant record anything.
                },
                function (\Throwable $exception) {
                    $this->log($exception);
                    if ($exception instanceof ServerException) {
                        // try again in 2 minutes
                        $this->release(120);
                    }
                },
                false
            );
        } else {
            $buildingId = $this->accountRelated['building_id'] ?? 'No building id!';
            Log::debug('Building ' . $buildingId . ' - Econobis calls are disabled, skipping call');
        }
    }

    public function middleware(): array
    {
        return [new EnsureCooperationHasEconobisLink(new Cooperation($this->cooperation))];
    }
}
