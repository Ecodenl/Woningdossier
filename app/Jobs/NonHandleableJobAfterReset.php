<?php

namespace App\Jobs;

use App\Contracts\Queue\ShouldNotHandleAfterBuildingReset;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Traits\Queue\RegisterQueuedJobTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class NonHandleableJobAfterReset implements ShouldQueue, ShouldNotHandleAfterBuildingReset
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RegisterQueuedJobTime;

    public function __construct()
    {
        $this->registerQueuedTime();
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building)];
    }
}