<?php

namespace App\Console\Commands\Upgrade;

use App\Models\User;
use App\Models\UserInterest;
use App\Services\ConsiderableService;
use Illuminate\Console\Command;

class MapUserInterestsToConsiderables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:user-interests-to-considerables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maps the user interests to the considerables.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userInterests = UserInterest::withoutGlobalScopes()->cursor();

        /** @var UserInterest $userInterest */
        foreach ($userInterests as $userInterest) {
            $considerable = false;
            // calculate_value = "maybe more information"
            if ($userInterest->interest->calculate_value >= 3) {
                $considerable = true;
            }
            ConsiderableService::save($userInterest->interestedIn, $userInterest->user, $userInterest->inputSource, ['is_considering' => $considerable]);
        }
    }
}
