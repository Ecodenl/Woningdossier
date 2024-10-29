<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendBuildingFilledInAnswersToEconobis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Gebruik extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:gebruik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the "gebruik" (filled in answers) to Econobis, will take all users that have changed their tool in the last 12 hours.';

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
     */
    public function handle(): int
    {
        $relevantLastChangedDate = Carbon::now()->subHours(24);

        // we don't have to use any policy, because we do this in the query itself.
        User::where('tool_last_changed_at', '>=', $relevantLastChangedDate)
            ->econobisContacts()
            ->where('allow_access', 1)
            ->chunkById(50, function ($users) {
                foreach ($users as $user) {
                    SendBuildingFilledInAnswersToEconobis::dispatch($user->building);
                }
            });
        return 0;
    }
}
