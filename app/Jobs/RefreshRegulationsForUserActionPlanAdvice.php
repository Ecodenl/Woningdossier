<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use App\Traits\Queue\HasNotifications;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RefreshRegulationsForUserActionPlanAdvice implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasNotifications;

    public $queue = Queue::APP_EXTERNAL;

    public UserActionPlanAdvice $userActionPlanAdvice;
    public Building $building;
    public InputSource $inputSource;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $this->userActionPlanAdvice = $userActionPlanAdvice;
        $this->building = $userActionPlanAdvice->user->building;
        $this->inputSource = $userActionPlanAdvice->inputSource;

        $this->queue = Queue::REGULATIONS;
        $this->setUuid();
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

    public function failed(Throwable $exception)
    {
        $this->deactivateNotification();
    }
}
