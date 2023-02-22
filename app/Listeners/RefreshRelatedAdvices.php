<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshRelatedAdvices implements ShouldQueue
{
    public $queue = Queue::REGULATIONS;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $measureModel = $event->measureModel;
        $advisableModels = [MeasureApplication::class, CustomMeasureApplication::class, CooperationMeasureApplication::class];
        if (in_array($measureModel->getMorphClass(), $advisableModels)) {
            $measureModel
                ->userActionPlanAdvices()
                ->withoutGlobalScopes()
                ->chunk(100, function ($userActionPlanAdvices) {
                    foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
                        RefreshRegulationsForUserActionPlanAdvice::dispatch($userActionPlanAdvice);
                    }
                });
        } else {
            throw new \Exception('Non advisable model provided.');
        }
    }
}
