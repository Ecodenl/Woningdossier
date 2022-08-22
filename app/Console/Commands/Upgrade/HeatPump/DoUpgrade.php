<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use Illuminate\Console\Command;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the application with all changes for the heat-pump';

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
            UpdateToolQuestions::class => [],
        ];

        foreach ($commands as $command => $params) {
            $this->info("Running command: {$command}");
            $this->call($command, $params);
        }
    }
}
