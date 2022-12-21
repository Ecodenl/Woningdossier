<?php

namespace App\Console\Commands\Upgrade\HeatPump\WaterCalc;

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
    protected $signature = 'upgrade:heat-pump:water-calc:do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the application with all changes for the heat pump water calculation change';

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
