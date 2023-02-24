<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Building;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRegulationsForBuildingUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Building $building;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        $this->building = $building;
        $this->queue = Queue::REGULATIONS;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        UserActionPlanAdviceService::init()->forUser($this->building->user)->refreshUserRegulations();
    }
}
