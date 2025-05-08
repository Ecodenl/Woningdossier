<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class InsertLogEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $loggableType, public int $loggableId, public int $buildingId, public string $message, public bool $crucial = false)
    {
        $this->queue = Queue::LOGS;
        // crucial = false -> if the job is already running, it will not be released back to the queue; It doesn't matter if the job is not ran again.
        // e.g. it doesn't matter if we have one or multiple "User X heeft een wijziging doorgevoerd in het actieplan" entries at the same timestamp.
        // crucial = true -> if the job is already running, it will be released back to the queue
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::create([
            'loggable_type' => $this->loggableType,
            'loggable_id' => $this->loggableId,
            'building_id' => $this->buildingId,
            'message' => $this->message,
        ]);
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        $withoutOverlapping = new WithoutOverlapping(sprintf('insert-log-entry-%s-%s-%s', $this->loggableType, $this->loggableId, $this->buildingId));
        if (! $this->crucial) {
            return [
                $withoutOverlapping->dontRelease(),
            ];
        }
        return [
            $withoutOverlapping->releaseAfter(10),
        ];
    }
}
