<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
     * @param object $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle($event)
    {
        $measureModel = $event->measureModel;
        $advisableModels = [
            MeasureApplication::class, CustomMeasureApplication::class, CooperationMeasureApplication::class,
        ];
        if (in_array($measureModel->getMorphClass(), $advisableModels)) {
            $measureModel->userActionPlanAdvices()
                ->withoutGlobalScope(SoftDeletingScope::class)
                ->withoutGlobalScope(GetValueScope::class)
                ->withoutGlobalScope(VisibleScope::class)
                ->chunk(100, function ($userActionPlanAdvices) {
                    foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
                        RefreshRegulationsForUserActionPlanAdvice::dispatch($userActionPlanAdvice);
                    }
                });
        } else {
            throw new Exception('Non advisable model provided.');
        }
    }
}
