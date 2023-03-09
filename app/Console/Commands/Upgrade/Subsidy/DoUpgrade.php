<?php

namespace App\Console\Commands\Upgrade\Subsidy;

use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncMeasures;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncTargetGroups;
use App\Helpers\Arr;
use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Support\Facades\Artisan;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:subsidy:do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the application with all changes for the lite scan';

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
        $commands = [
            SyncMeasures::class => [],
            SyncTargetGroups::class => [],
            SeedCommand::class => [
                ['--class' => 'CooperationPresetSeeder', '--force' => true],
                ['--class' => 'CooperationPresetContentSeeder', '--force' => true],
                ['--class' => 'ToolQuestionsTableSeeder', '--force' => true],
                ['--class' => 'SubSteppablesTableSeeder', '--force' => true],
                ['--class' => 'MeasureApplicationsTableSeeder', '--force' => true],
            ],
            FixContractType::class => [],
            SeedBagMunicipalityMapping::class => [],
            SeedMeasureCategoryMapping::class => [],
            ClearCommand::class => [],
        ];

        Artisan::call('translations:import', ['--only-groups' => 'pdf/user-report']);

        foreach ($commands as $command => $variants) {
            if (! is_array(Arr::first($variants))) {
                $variants = [$variants];
            }

            foreach ($variants as $params) {
                $this->info("Running command: {$command}");
                $this->call($command, $params);
            }
        }
    }
}
