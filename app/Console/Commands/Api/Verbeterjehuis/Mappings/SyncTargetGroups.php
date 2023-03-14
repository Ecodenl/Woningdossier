<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Helpers\Wrapper;
use App\Models\Mapping;
use App\Models\ToolQuestion;
use App\Services\DiscordNotifier;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Client;
use App\Services\Verbeterjehuis\RegulationService;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Illuminate\Console\Command;

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
     * @return int
     */
    public function handle(MappingService $mappingService)
    {
        Wrapper::wrapCall(function () use ($mappingService) {
            $map = [
                'bought' => 'Woningeigenaar',
                'rented' => 'Huurder',
                'rented-private' => 'Huurder'
            ];

            $targetGroups = collect(
                RegulationService::init()->getFilters()['TargetGroups']
            )->keyBy('Value');

            foreach ($map as $from => $target) {
                $mappingService->from(
                    ToolQuestion::findByShort('building-contract-type')
                        ->toolQuestionCustomValues()
                        ->where('short', $from)
                        ->first()
                )->sync([$targetGroups[$target]]);
            }
            DiscordNotifier::init()->notify('SyncTargetGroups just ran!');
        }, function ($exception) {
            $this->error('Something is going on with VerbeterJeHuis!');
        });

        return 0;
    }
}
