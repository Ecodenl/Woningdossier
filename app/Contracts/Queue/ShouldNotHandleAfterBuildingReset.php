<?php

namespace App\Contracts\Queue;

use Carbon\Carbon;

interface ShouldNotHandleAfterBuildingReset
{
    public function registerQueuedTime(): void;

    public function queuedAt(): Carbon;
}
