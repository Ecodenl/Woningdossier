<?php

namespace App\Jobs;

use App\Models\UserActionPlanAdvice;
use App\Services\Verbeterjehuis\RegulationService;
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
        $userActionPlanAdvice = $this->userActionPlanAdvice;

        $payload = RegulationService::init()
            ->forBuilding($userActionPlanAdvice->user->building)
            ->get();

        $regulations = $payload
            ->forMeasureApplication($userActionPlanAdvice->userActionPlanAdvisable)
            ->forBuildingContractType($userActionPlanAdvice->user->building, $userActionPlanAdvice->inputSource);

        if ($regulations->getLoans()->isNotEmpty()) {
            $userActionPlanAdvice->loan_available = true;
        }
        if ($regulations->getSubsidies()->isNotEmpty()) {
            $userActionPlanAdvice->subsidy_available = true;
        }
    }
}
