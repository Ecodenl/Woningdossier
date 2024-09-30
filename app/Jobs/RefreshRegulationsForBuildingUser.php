<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Building;
use App\Models\User;
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
     */
    public function handle(): void
    {
        Log::debug('Handle of refresh regulations for building user');
        $user = $this->building->user;

        // If building was deleted, user will be gone, and so it _can_ be null, even if it doesn't happen often
        if ($user instanceof User) {
            UserActionPlanAdviceService::init()
                ->forUser($user)
                ->refreshUserRegulations();
        }
    }
}
