<?php

namespace App\Listeners;

use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
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
        $id = $event->id;
        $queuedClass = $event->job->class;
    }

    public function logBefore($event)
    {
        $payload = $event->job->payload();
        $command = unserialize($payload['data']['command']);
        $commandTraits = class_uses_recursive($command);
        $jobName = get_class($command);
        if (in_array(HasNotifications::class, $commandTraits)) {
            $building = $command->building ?? $command->user->building;
            Log::debug("JOB {$jobName} started | b_id: {$building->id} | input_source_id: {$command->inputSource->id}");
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
            NotificationService::init()
                ->forBuilding($building)
                ->forInputSource($command->inputSource)
                ->setType($jobName)
                ->setUuid($command->uuid)
                ->deactivate();
        }
    }
    public function subscribe(Dispatcher $events): array
    {
        return [
            JobQueued::class => ['cacheTimeOfQueued'],
            JobProcessing::class => ['logBefore'],
            JobProcessed::class => ['deactivateNotification']
        ];
    }
}
