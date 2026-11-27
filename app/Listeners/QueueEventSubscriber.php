<?php

namespace App\Listeners;

use App\Traits\Queue\HasNotifications;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function handleJobProcessed(JobProcessed $event): void
    {
        // deactivate notification
        $payload = $event->job->payload();
        $command = unserialize($payload['data']['command']);
        $commandTraits = class_uses_recursive($command);
        $jobName = get_class($command);
        if (in_array(HasNotifications::class, $commandTraits)) {
            $building = $command->building ?? $command->user->building;
            Log::debug("JOB {$jobName} ended | b_id: {$building->id} | input_source_id: {$command->inputSource->id}");

            $command->deactivateNotification();
        }
    }

    /*public function subscribe($events): array
    {
        return [
            JobProcessed::class => 'handleJobProcessed',
        ];
    }*/
}
