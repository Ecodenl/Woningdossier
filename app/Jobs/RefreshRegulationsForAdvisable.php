<?php

namespace App\Jobs;

use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRegulationsForAdvisable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?Model $measureModel;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Model $measureModel)
    {
        $this->measureModel = $measureModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $advisableModels = [MeasureApplication::class, CustomMeasureApplication::class, CooperationMeasureApplication::class];
        if (in_array($this->measureModel->getMorphClass(), $advisableModels)) {
            $this->measureModel
                ->userActionPlanAdvices()
                ->withoutGlobalScopes()
                ->chunk(100, function ($advices) {
                    foreach ($advices as $advice) {
                        UserActionPlanAdviceService::init()->refreshRegulations($advice);
                    }
                });
        } else {
            throw new \Exception('Non advisable model provided.');
        }
    }
}
