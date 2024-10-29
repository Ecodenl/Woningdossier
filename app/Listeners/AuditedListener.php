<?php

namespace App\Listeners;

use App\Helpers\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OwenIt\Auditing\Events\Audited;

class AuditedListener implements ShouldQueue
{
    public $queue = Queue::LOGS;

    /**
     * Create the Auditing event listener.
     */
    public function __construct()
    {
        // ...
    }

    public function handle(Audited $event): void
    {
        // the audit model
        $audit = $event->audit;

        $audit->update([
            'building_id' => $event->model->building_id,
            'input_source_id' => $event->model->input_source_id
        ]);
    }
}