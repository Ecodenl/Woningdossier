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
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            Log::debug('SmartTwin calls are disabled, skipping DeleteAccount for ' . $this->smartTwinUserId);
            return;
        }

        $api->user()->delete($this->smartTwinUserId);
    }
}
