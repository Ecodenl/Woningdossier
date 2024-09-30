<?php

namespace App\Console\Commands\Upgrade\Econobis;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Support\Arr;

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
    public function handle(): void
    {
        $commands = [
            SeedCommand::class => [
                ['--class' => 'ToolQuestionsTableSeeder', '--force' => true],
                ['--class' => 'RelatedModelSeeder', '--force' => true],
                ['--class' => 'IntegrationsTableSeeder', '--force' => true],
            ],
        ];

        foreach ($commands as $command => $variants) {
            if ( ! is_array(Arr::first($variants))) {
                $variants = [$variants];
            }

            foreach ($variants as $params) {
                $this->info("Running command: {$command}");
                $this->call($command, $params);
            }
        }
    }
}
