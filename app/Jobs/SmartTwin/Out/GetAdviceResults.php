<?php

namespace App\Jobs\SmartTwin\Out;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GetAdviceResults implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected array $callbackData,
        protected int $buildingId,
    ) {
    }

    public function uniqueId(): string
    {
        $eventType = $this->callbackData['EventType'] ?? 'unknown';

        return "{$eventType}_{$this->buildingId}";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
