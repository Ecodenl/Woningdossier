<?php

namespace App\Contracts\Queue;

use Illuminate\Support\Carbon;

interface ShouldNotHandleAfterBuildingReset
{
    public function registerQueuedTime(): void;

    public function queuedAt(): Carbon;
}
