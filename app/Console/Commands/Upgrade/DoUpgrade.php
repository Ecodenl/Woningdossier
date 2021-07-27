<?php

namespace App\Console\Commands\Upgrade;

use App\Console\Commands\AddQuestionsToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all commands needed to do the upgrade';

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
        if ($this->confirm('This will 100% mess up your environment when ran unintentionally, do you want to continue')) {
            $this->info('<fg=yellow>May the force be with you...</>');
            $seeders = [
                \StepsTableSeeder::class,
                \ToolQuestionTypesTableSeeder::class,
                \SubStepTemplatesTableSeeder::class,

            ];

            foreach ($seeders as $seeder) {
                $this->info("Running $seeder");
                Artisan::call('db:seed', ['--class' => $seeder]);
            }

            $commands = [
                AddQuestionsToDatabase::class
            ];

            foreach ($commands as $command) {
                $this->info("Running $command");
                Artisan::call($command);
            }
        } else {
            $this->info('K bye');
        }
    }
}
