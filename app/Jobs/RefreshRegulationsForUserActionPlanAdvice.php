<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use App\Traits\Queue\HasNotifications;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Bus\Batchable;

class RefreshRegulationsForUserActionPlanAdvice extends NonHandleableJobAfterReset
{
    use Batchable, HasNotifications;

    public UserActionPlanAdvice $userActionPlanAdvice;
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
        $this->userActionPlanAdvice = $userActionPlanAdvice;
        $this->building = $userActionPlanAdvice->user->building;
        $this->inputSource = $userActionPlanAdvice->inputSource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            UserActionPlanAdviceService::init()
                ->forUser($this->userActionPlanAdvice->user)
                ->refreshRegulations($this->userActionPlanAdvice);
        } catch (ConnectException|ServerException $connectException) {
            $this->release(10);
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->deactivateNotification();
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->userActionPlanAdvice->user->building)];
    }
}
