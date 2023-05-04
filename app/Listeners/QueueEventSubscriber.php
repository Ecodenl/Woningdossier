<?php

namespace App\Listeners;

use App\Jobs\PdfReport;
use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
use Carbon\Carbon;
use Illuminate\Bus\Events\BatchDispatched;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function cacheTimeOfQueuedJob($event)
    {
        Log::debug('JOB QUEUED EVENT HIT');
        // i think we can use this for the cache key, we will also retrieve
        if ($event->connectionName !== "sync") {
            $id = $event->id;
            $job = $event->job;

            if ($event->job instanceof CallQueuedListener) {
                $displayName = $job->class;
            } else {
                $displayName = get_class($job);
            }

            $date = Carbon::now()->format('Y-m-d H:i:s');
            Log::debug("{$displayName} [{$id}] Caching time: {$date}");
            Cache::set($id, $date);
        }
    }

    public function cacheTimeOfQueuedBatchedJob(BatchDispatched $event)
    {
            $id = $event->batch->toArray()['id'];

            // the name is optional when dispatching.
            $displayName = $event->batch->toArray()['name'] ?? 'Unknown batched job';

            $date = Carbon::now()->format('Y-m-d H:i:s');
            Log::debug("{$displayName} [{$id}] Caching time: {$date}");
            Cache::set($id, $date);
    }

    public function deactivateNotification($event)
    {
        $payload = $event->job->payload();
        $command = unserialize($payload['data']['command']);
        $commandTraits = class_uses_recursive($command);
        $jobName = get_class($command);
        if (in_array(HasNotifications::class, $commandTraits)) {
            $building = $command->building ?? $command->user->building;
            Log::debug("JOB {$jobName} ended | b_id: {$building->id} | input_source_id: {$command->inputSource->id}");

            $service = NotificationService::init()
                ->forBuilding($building)
                ->setType($jobName)
                ->setUuid($command->uuid);

            // The command might not care about the input source, and so in that case we don't want to query on it.
            if ($command->caresForInputSource) {
                $service->forInputSource($command->inputSource);
            }

            $service->deactivate();
        }
    }

    public function forgetCachedQueuedTime($event)
    {
        if ($event->connectionName !== "sync") {
            Cache::forget($event->job->getJobId());
        }
    }

    public function subscribe($events): array
    {
        return [
            BatchDispatched::class => ['cacheTimeOfQueuedBatchedJob'],
            JobQueued::class => ['cacheTimeOfQueuedJob'],
            JobProcessed::class => ['deactivateNotification', 'forgetCachedQueuedTime']
        ];
    }
}
