<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $smartTwinUserId)
    {
        $this->queue = Queue::APP_EXTERNAL;
    }

    public function handle(): void
    {
        // TODO: implement SmartTwin API DELETE call for $this->smartTwinUserId.
    }
}
