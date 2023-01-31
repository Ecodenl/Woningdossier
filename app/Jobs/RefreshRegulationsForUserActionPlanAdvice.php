<?php

namespace App\Jobs;

use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRegulationsForUserActionPlanAdvice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userActionPlanAdvice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $this->userActionPlanAdvice = $userActionPlanAdvice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        UserActionPlanAdviceService::init()->refreshRegulations($this->userActionPlanAdvice);
    }
}
