<?php

namespace App\Listeners;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Queue;
use App\Jobs\InsertLogEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class StepDataHasBeenChangedListener
{
    public $queue = Queue::LOGS;

    /**
     * Handle the event.
     */
    public function handle(StepDataHasBeenChanged $event): void
    {
        dispatch(new InsertLogEntry(User::class, $event->user->id, $event->building->id, __('woningdossier.log-messages.step-data-has-been-changed', [
            'full_name' => $event->user->getFullName(),
            'time' => Carbon::now(),
        ])));
    }
}
