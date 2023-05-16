<?php

namespace App\Listeners;

use App\Jobs\PdfReport;
use App\Services\Models\NotificationService;
use App\Traits\Queue\HasNotifications;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Events\BatchDispatched;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueEventSubscriber
{
    public function cacheTimeOfQueuedJob(JobQueued $event)
    {
        Log::debug('job queued!');
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

         Log::debug(get_class($event)."[{$event->job->id}]".' uuid: '.$event->job->uuid);
        $this->logState($event);
    }

    public function cacheTimeOfQueuedBatchedJob(BatchDispatched $event)
    {
        $id = $event->batch->id;

        // the name is optional when dispatching.
        $displayName = $event->batch->toArray()['name'] ?? 'Unknown batched job';

        $date = Carbon::now()->format('Y-m-d H:i:s');
        Log::debug("{$displayName} [{$id}] Caching time: {$date}");
        Cache::set($id, $date);
    }


//    /**
//     * @param  JobProcessed  $event
//     * @return void
//     */
//    public function forgetCachedQueuedTime($event)
//    {
//        if ($event->connectionName !== "sync") {
//            $this->logState($event);
//            Cache::forget($event->job->getJobId());
//        }
//    }

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
    public function jobProcessing(JobProcessing $event)
    {
        // consistent check if its a batched job.
        $batchId = unserialize($event->job->payload()['data']['command'])->batchId ?? null;
        if (is_null($batchId)) {
            // this does not make sense at first, let me explain:
            // when the job is queued we set the datetime on the job id, this is because at that time the uuid is not available.
            // The job id itself may change because of a release, so the next "job processing" we have no memory of the first id / job.
            // But upon processing a job uuid becomes available! The uuid will not change with a release and stays the same
            // so here we will convert the "original" jobId to the uuid.
            $jobQueuedAt = Cache::get($event->job->getJobId());
            if (!is_null($jobQueuedAt)) {
                Log::debug('Resetting cache '.$event->job->getJobId().' => ' .$event->job->uuid());
                Cache::set($event->job->uuid(), $jobQueuedAt);
                Cache::forget($event->job->getJobId());
            }
        }

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
            JobProcessing::class => ['jobProcessing'],
            JobProcessed::class => ['deactivateNotification'],
            JobFailed::class => 'jobFailedHandle',
            JobExceptionOccurred::class => 'jobExceptionHandle',
        ];
    }

    private function logState($event)
    {
        $context = [];
        $originalJobId = $event->id ?? 'No id';
        $jobUuid =  'No uuid';
        $usesBatch = in_array(Batchable::class, class_uses($event->job), true);
        if ( ! $usesBatch) {
            $isReleased = null;
            $hasFailed = null;
            $isDeleted = null;
            $isDeletedOrReleased = null;
            if ( ! $event instanceof JobQueued) {
                $isReleased = $event->job->isReleased() ?? null;
                $hasFailed = $event->job->hasFailed() ?? null;
                $isDeleted = $event->job->isDeleted() ?? null;
                $isDeletedOrReleased = $event->job->isDeletedOrReleased() ?? null;
                $originalJobId = $event->job->getJobId();
                $jobUuid = $event->job->uuid();
            }
            $context = compact('isReleased', 'hasFailed', 'isDeleted', 'isDeletedOrReleased');

        } else {
            // dd($event->job->batch() instanceof Batch);
        }


        if ($event->connectionName !== "sync") {
            Log::debug(
                get_class($event).'['.get_class($event->job).']'." "."[{$originalJobId}]".' uuid: '.$jobUuid,
                $context
            );
        }
    }
}

