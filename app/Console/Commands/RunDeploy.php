<?php

namespace App\Console\Commands;

use App\Console\Commands\Upgrade\AddSmartTwinGlazingOptions;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;

class RunDeploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
    This command contains code that should be ran during deployment. 
    It also MUST contain atomic code to prevent problems in case this command isn\'t cleared for a new deployment.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // In order: the questions take their options from the tables filled before them.
        $commands = [
            [SeedCommand::class, ['--class' => 'ToolQuestionTypesTableSeeder', '--force' => true]],
            [SeedCommand::class, ['--class' => 'ToolLabelsTableSeeder', '--force' => true]],
            // The energy-label question takes its options from this table.
            [SeedCommand::class, ['--class' => 'EnergyLabelsTableSeeder', '--force' => true]],
            // The glass and frame questions link the options this adds.
            [AddSmartTwinGlazingOptions::class, []],
            [SeedCommand::class, ['--class' => 'ToolQuestionsTableSeeder', '--force' => true]],
            [SeedCommand::class, ['--class' => 'SubSteppablesTableSeeder', '--force' => true]],
            [SeedCommand::class, ['--class' => 'MeasureApplicationsTableSeeder', '--force' => true]],
            [SeedCommand::class, ['--class' => 'InputSourcesTableSeeder', '--force' => true]],
        ];

        foreach ($commands as [$command, $arguments]) {
            $this->call($command, $arguments);
        }

        return self::SUCCESS;
    }
}
