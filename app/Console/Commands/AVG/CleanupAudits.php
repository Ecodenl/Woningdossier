<?php

namespace App\Console\Commands\AVG;

use App\Models\BuildingFeature;
use App\Services\DiscordNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use OwenIt\Auditing\Models\Audit;

class CleanupAudits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avg:cleanup-audits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup the old audits';

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
    public function handle(DiscordNotifier $discordNotifier)
    {
        $deleteCount = Audit::where('created_at', '<=', Carbon::now()->subMonths(4))->delete();
        if ($deleteCount > 0) {
            $discordNotifier->notify("Deleted {$deleteCount} 4 month old audits.");
        }
    }
}
