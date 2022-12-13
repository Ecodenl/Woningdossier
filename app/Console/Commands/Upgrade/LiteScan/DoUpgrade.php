<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Helpers\Arr;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:do';

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
            DeleteCompletedStep::class => [],
            UpdateCooperationMeasures::class => [],
            SeedCommand::class => [
                ['--class' => 'ScansTableSeeder', '--force' => true],
                ['--class' => 'StepsTableSeeder', '--force' => true],
                ['--class' => 'ToolLabelsTableSeeder', '--force' => true],
                ['--class' => 'ToolQuestionsTableSeeder', '--force' => true],
                ['--class' => 'SubSteppablesTableSeeder', '--force' => true],
            ],
            GiveCooperationDefaultScans::class => [],
            ConvertQuestionnaireStepsToPivot::class => [],
        ];

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
