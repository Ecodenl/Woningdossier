<?php

namespace App\Traits\Queue;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait RegisterQueuedJobTime
{
    use HasJobUuid;

    public bool $isDone = true;

    public function registerQueuedTime(): void
    {
        $jobUuid = $this->getJobUuid();

        $date = Carbon::now()->format('Y-m-d H:i:s');

        if ( ! app()->environment('production')) {
            $displayName = get_class($this);
            Log::debug("{$displayName} [{$jobUuid}] Caching time: {$date}");
        }
        Cache::set($jobUuid, $date);
    }
}