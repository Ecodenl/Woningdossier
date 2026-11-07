<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public string $smartTwinUserId)
    {
        $this->queue = Queue::APP_EXTERNAL;
    }

    public function handle(SmartTwinApi $api): void
    {
        // Not per cooperation: the SmartTwin user exists whatever the cooperations of the account
        // think of SmartTwin now, and is removed with the account.
        if (! Hoomdossier::hasSmartTwinApi()) {
            Log::debug('SmartTwin calls are disabled, skipping DeleteAccount for ' . $this->smartTwinUserId);
            return;
        }

        $api->user()->delete($this->smartTwinUserId);
    }
}
