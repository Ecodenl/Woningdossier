<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Wrapper;
use App\Jobs\Middleware\EnsureCooperationHasEconobisLink;
use App\Models\Cooperation;
use App\Services\Econobis\Api\EconobisApi;
use App\Services\Econobis\EconobisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Predis\Response\ServerException;

class SendUserDeletedToEconobis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CallsEconobisApi;

    public array $accountRelated;
    public Cooperation $cooperation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cooperation $cooperation, $accountRelated)
    {
        $this->cooperation = $cooperation;
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
                // this cant work right ?
                ->forCooperation($this->cooperation)
                ->hoomdossier()
                ->delete(
                    $econobisService
                        ->setAccountRelated($this->accountRelated)
                        ->getPayload()
                );
        });
    }

    public function wrapCall(\Closure $function)
    {
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
            }, false);

        return;
    }

    public function middleware(): array
    {
        return [new EnsureCooperationHasEconobisLink($this->cooperation)];
    }
}
