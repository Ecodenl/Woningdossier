<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Queue;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCoachAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user)
    {
        $this->queue = Queue::APP_EXTERNAL;
    }

    public function handle(): void
    {
        // TODO: implement SmartTwin API call to create a coach-type account for $this->user.
    }
}
