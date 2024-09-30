<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Models\Log;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserAssociatedWithOtherCooperation implements ShouldQueue
{
    public $queue = Queue::LOGS;
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event): void
    {
        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $event->user->id,
            'building_id' => $event->user->building->id,
            'message' => __('woningdossier.log-messages.user-associated-with-other-cooperation', [
                'full_name' => $event->user->getFullName(),
                'cooperation_name' => $event->cooperation->name,
            ]),
        ]);
    }
}
