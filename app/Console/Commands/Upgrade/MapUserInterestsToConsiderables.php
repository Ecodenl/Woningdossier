<?php

namespace App\Console\Commands\Upgrade;

use App\Console\Commands\BenchmarkCommand;
use App\Models\UserInterest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapUserInterestsToConsiderables extends Command
{
    use BenchmarkCommand;

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

        $this->startTimer();
        /** @var UserInterest $userInterest */
        foreach ($userInterests as $userInterest) {
            $considerable = false;
            // calculate_value = "maybe more information and everything "below it", aka yes on short term, yes on quick term.
            if ($userInterest->interest->calculate_value <= 3) {
                $considerable = true;
            }

            DB::table('considerables')->insert([
                'user_id' => $userInterest->user_id,
                'input_source_id' => $userInterest->input_source_id,
                'considerable_id' => $userInterest->interested_in_id,
                'considerable_type' => $userInterest->interested_in_type,
                'is_considering' => $considerable
            ]);
        }

        $time = $this->stopTimer();
        $this->info("Execution took {$time} seconds");
    }
}
