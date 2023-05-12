<?php

namespace App\Listeners;

use App\Jobs\PdfReport;
use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Events\BatchDispatched;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function cacheTimeOfQueuedJob(JobQueued $event)
    {
//        Log::debug('JOB QUEUED EVENT HIT');
        // i think we can use this for the cache key, we will also retrieve
//        if ($event->connectionName !== "sync") {
//            $id = $event->id;
//            $job = $event->job;
//
//            if ($event->job instanceof CallQueuedListener) {
//                $displayName = $job->class;
//            } else {
//                $displayName = get_class($job);
//            }
//
//            $date = Carbon::now()->format('Y-m-d H:i:s');
//            Log::debug("{$displayName} [{$id}] Caching time: {$date}");
//            Cache::set($id, $date);
//        }
//        dd($event);
        // Log::debug(get_class($event)."[{$event->job->id}]".' uuid: '.$event->job->uuid);
        $this->logState($event);
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


    /**
     * @param  JobProcessed  $event
     * @return void
     */
    public function forgetCachedQueuedTime($event)
    {
        if ($event->connectionName !== "sync") {
            $this->logState($event);
            Cache::forget($event->job->getJobId());
        }
    }

    /**
     * @param  JobFailed  $event
     * @return void
     */
    public function jobFailedHandle($event)
    {
        // not possible to access methods from the job self, so we will retrieve the uuid manually
        $this->logState($event);
        // Log::debug(get_class($event)."[{$event->job->getJobId()}]".' uuid: '.$id);
    }

    /**
     * @param  JobFailed  $event
     * @return void
     */
    public function jobExceptionHandle($event)
    {
        // not possible to access methods from the job self, so we will retrieve the uuid manually
        $this->logState($event);
        // Log::debug(get_class($event)."[{$event->job->getJobId()}]".' uuid: '.$id);
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

    public function subscribe($events): array
    {
        return [
            BatchDispatched::class => ['cacheTimeOfQueuedBatchedJob'],
            JobQueued::class => ['cacheTimeOfQueuedJob'],
            JobProcessed::class => ['deactivateNotification', 'forgetCachedQueuedTime'],
            JobFailed::class => 'jobFailedHandle',
            JobExceptionOccurred::class => 'jobExceptionHandle',
        ];
    }

    private function logState(JobQueued $event)
    {
        $context = [];
        $originalJobId = null;
        $jobUuid = null;
        if (!$event->job->batch() instanceof Batch) {
            $isReleased = $event->job->isReleased() ?? null;
            $hasFailed = $event->job->hasFailed() ?? null;
            $isDeleted = $event->job->isDeleted() ?? null;
            $isDeletedOrReleased = $event->job->isDeletedOrReleased() ?? null;
            $context = compact('isReleased', 'hasFailed', 'isDeleted', 'isDeletedOrReleased');
            $originalJobId = $event->job->getJobId();
            $jobUuid = $event->job->uuid();
        }

        if ($event->connectionName !== "sync") {
            Log::debug(
                get_class($event).'['.get_class($event->job).']'." "."[{$originalJobId}]".' uuid: '.$jobUuid,
                $context
            );
        }
    }
}

