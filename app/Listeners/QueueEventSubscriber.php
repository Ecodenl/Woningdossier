<?php

namespace App\Listeners;

use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function deactivateNotification(JobProcessed $event)
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

    public function subscribe($events): array
    {
        return [
            JobProcessed::class => ['deactivateNotification'],
        ];
    }
}
