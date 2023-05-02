<?php

namespace App\Listeners;

use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
use Carbon\Carbon;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function cacheTimeOfQueued($event)
    {
        // i think we can use this for the cache key, we will also retrieve
        if ($event->connectionName !== "sync") {
            $id = $event->id;

            if ($event->job instanceof CallQueuedListener) {
                $displayName = $event->job->class;
            } else {
                $displayName = get_class($event->job);
            }

            $date = Carbon::now()->format('Y-m-d H:i:s');
            Log::debug("{$displayName} [{$id}] Caching time: {$date}");
            Cache::set($id, $date);
        }
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

        Cache::forget($event->job->payload()['id']);
    }

    public function subscribe($events): array
    {
        return [
            JobQueued::class => ['cacheTimeOfQueued'],
//            JobProcessing::class => ['logBefore'],
            JobProcessed::class => ['deactivateNotification']
        ];
    }
}
