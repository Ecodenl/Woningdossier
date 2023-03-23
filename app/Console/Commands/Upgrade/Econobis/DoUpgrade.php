<?php

namespace App\Console\Commands\Upgrade\Econobis;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:econobis:do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all commands for the Econobis upgrade.';

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
            SeedCommand::class => ['--class' => 'IntegrationsTableSeeder', '--force' => true],
        ];

        foreach ($commands as $command => $arguments) {
            $this->call($command, $arguments);
        }
    }
}
