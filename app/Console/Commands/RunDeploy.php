<?php

namespace App\Console\Commands;

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
    It also MUST contain atomic code to prevent problems in case this command isnt cleared for a new deployment.';

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
            SeedCommand::class => ['--class' => 'MeasureApplicationsTableSeeder', '--force' => true],
            UpdateToolQuestions::class => [],
        ];

        foreach ($commands as $command => $arguments) {
            $this->call($command, $arguments);
        }
    }
}
