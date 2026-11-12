<?php

namespace App\Jobs;

use App\Exceptions\VerbeterjehuisWafBlockException;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use App\Traits\Queue\HasNotifications;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Bus\Batchable;

class RefreshRegulationsForUserActionPlanAdvice extends NonHandleableJobAfterReset
{
    use Batchable, HasNotifications;

    // The advice is kept as an ID rather than a model: a recalculation deletes and recreates the advices while
    // this job is still queued, and a missing model would make the job (and its notification) vanish silently.
    public int $userActionPlanAdviceId;
    public Building $building;
    public InputSource $inputSource;

    public $tries = 3;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserActionPlanAdvice $userActionPlanAdvice)
    {
        parent::__construct();
        $this->setUuid();
        $this->ignoreNotificationInputSource();
        $this->onQueue(Queue::APP_EXTERNAL);
        $this->userActionPlanAdviceId = $userActionPlanAdvice->id;
        $this->building = $userActionPlanAdvice->user->building;
        $this->inputSource = $userActionPlanAdvice->inputSource;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userActionPlanAdvice = UserActionPlanAdvice::withoutGlobalScopes()->find($this->userActionPlanAdviceId);

        // The advice has been deleted since this job was queued, so there is nothing left to refresh.
        if (! $userActionPlanAdvice instanceof UserActionPlanAdvice) {
            return;
        }

        try {
            UserActionPlanAdviceService::init()
                ->forUser($userActionPlanAdvice->user)
                ->refreshRegulations($userActionPlanAdvice);
        } catch (ConnectException | ServerException $connectException) {
            // Server errors (5xx) and connection issues are temporary - retry
            $this->release(10);
        } catch (ClientException $clientException) {
            // The Verbeterjehuis API sits behind a Cloudflare WAF that intermittently
            // blocks us, returning an HTML 403 (or a 429/408 when rate-limited) instead
            // of the usual JSON. That is a genuine failure to refresh, so we let the job
            // fail honestly by wrapping it in a dedicated exception.
            //
            // The only problem this causes is volume: while a block is active every
            // queued advice hits the same error - potentially thousands at once. We do
            // not paper over that by faking success; instead VerbeterjehuisWafBlockException
            // throttles its own reporting (see its report() method), so Sentry receives a
            // single heartbeat per window instead of thousands of identical events.
            throw_if(in_array($clientException->getResponse()->getStatusCode(), [403, 408, 429], true), new VerbeterjehuisWafBlockException($clientException));

            // Any other client error (4xx) is a genuine problem - let it surface.
            throw $clientException;
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->deactivateNotification();
    }
}
