<?php

namespace App\Traits\Queue;

use Carbon\Carbon;

trait RegisterQueuedJobTime
{
    public Carbon $queuedAt;

    public function registerQueuedTime(): void
    {
        $this->queuedAt = Carbon::now();
    }

    public function queuedAt(): Carbon
    {
        return $this->queuedAt;
    }
}
