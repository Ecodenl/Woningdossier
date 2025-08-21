<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Helpers\Wrapper;
use App\Models\ToolQuestion;
use App\Services\DiscordNotifier;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncTargetGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:verbeterjehuis:mapping:sync-target-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map our ToolQuestionCustomValues to the correct target groups.';

    /**
     * Execute the console command.
     */
    public function handle(MappingService $mappingService): int
    {
        Wrapper::wrapCall(function () use ($mappingService) {
            $map = [
                'bought' => 'Woningeigenaar',
                'rented' => 'Huurder',
                'rented-private' => 'Particuliere woningverhuurder'
            ];

            $targetGroups = collect(
                RegulationService::init()->getFilters()['TargetGroups']
            )->keyBy('Value');

            foreach ($map as $from => $target) {
                if (! $targetGroups->has($target)) {
                    DiscordNotifier::init()->notify("Sync target groups for {$target} not found!");
                    Log::info('Target groups:');
                    Log::info($targetGroups);
                    continue;
                }

                $mappingService->from(
                    ToolQuestion::findByShort('building-contract-type')
                        ->toolQuestionCustomValues()
                        ->where('short', $from)
                        ->first()
                )->sync([$targetGroups[$target]]);
            }

            $this->info("Measures mapped to building-contract-type question custom values.");
            Log::debug("Measures mapped to building-contract-type question custom values.");
        }, function () {
            $this->error('Something is going on with VerbeterJeHuis!');
        });

        return self::SUCCESS;
    }
}
