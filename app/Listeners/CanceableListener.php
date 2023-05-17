<?php

namespace App\Listeners;

use App\Contracts\Queue\ShouldNotHandleAfterBuildingReset;
use App\Traits\Queue\CheckLastResetAt;
use App\Traits\Queue\RegisterQueuedJobTime;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class CanceableListener implements ShouldQueue, ShouldNotHandleAfterBuildingReset
{
    use CheckLastResetAt, RegisterQueuedJobTime;
}