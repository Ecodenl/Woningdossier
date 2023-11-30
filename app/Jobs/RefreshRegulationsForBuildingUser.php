<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Building;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Facades\Log;

class RefreshRegulationsForBuildingUser extends NonHandleableJobAfterReset
{
    public Building $building;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        parent::__construct();
        $this->queue = Queue::APP_HIGH;
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
        $user->update(['refreshing_regulations' => true]);

        UserActionPlanAdviceService::init()
            ->forUser($user)
            ->refreshUserRegulations();
    }
}
