<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Jobs\InsertLogEntry;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class StepDataHasBeenChangedListener
{
    public $queue = Queue::LOGS;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(object $event): void
    {
        dispatch(new InsertLogEntry(User::class, $event->user->id, $event->building->id, __('woningdossier.log-messages.step-data-has-been-changed', [
            'full_name' => $event->user->getFullName(),
            'time' => Carbon::now(),
        ])));
    }
}
