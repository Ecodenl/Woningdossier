<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Models\Building;
use Illuminate\Console\Command;
use App\Jobs\SmartTwin\Out\GetAdviceResults as GetAdviceResultsJob;

class GetAdviceResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:smarttwin:get-advice-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fallback cron to get advice results from the SmartTwin API which might not have been fetched by a job earlier';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Building::containsPendingSmartTwinAdvices()->chunk(5, function ($buildings) {
            /** @var Building $building */
            foreach ($buildings as $building) {
                $this->line("Fallback cron to get advice results for " . $building->getKey());

                foreach ($building->getSmartTwinCallbacks() as $callback) {
                    GetAdviceResultsJob::dispatchSync($callback);
                }
            }
        });
    }
}
