<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Models\Building;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshRegulationsForBuildingUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = Queue::APP_HIGH;

    public Building $building;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Handle of refresh regulations for building user');
        $user = $this->building->user;

        UserActionPlanAdviceService::init()
            ->forUser($user)
            ->refreshUserRegulations();
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building)];
    }
}
