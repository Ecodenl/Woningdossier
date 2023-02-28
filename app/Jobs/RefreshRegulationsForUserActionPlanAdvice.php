<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRegulationsForUserActionPlanAdvice implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userActionPlanAdvice;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $this->userActionPlanAdvice = $userActionPlanAdvice;
        $this->queue = Queue::REGULATIONS;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            UserActionPlanAdviceService::init()->refreshRegulations($this->userActionPlanAdvice);
        } catch (ConnectException|ServerException $connectException) {
            $this->release(10);
        }
    }
}
