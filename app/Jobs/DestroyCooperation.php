<?php

namespace App\Jobs;

use App\Helpers\Models\CooperationHelper;
use App\Helpers\Queue;
use App\Models\Cooperation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DestroyCooperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Cooperation $cooperation)
    {
        $this->onQueue(Queue::APP);
    }

    public function handle(): void
    {
        CooperationHelper::destroyCooperation($this->cooperation);
    }
}
