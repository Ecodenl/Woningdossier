<?php

namespace App\Console\Commands\Upgrade;

use App\Console\Commands\AddQuestionsToDatabase;
use App\Console\Commands\ConvertUuidTranslationsToJson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DoUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:do  {--force}';

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
        $force = $this->option('force') ?? false;
        if ($force || $this->confirm('This will 100% mess up your environment when ran unintentionally, do you want to continue')) {
            $this->info('<fg=yellow>May the force be with you...</>');

            if (app()->environment() == 'local') {
                $this->info('Rolling back migrations..');
                Artisan::call('migrate:rollback');
                $this->info('Running migrations..');
                Artisan::call('migrate');
            }
            $beforeCommands = [
                ConvertUuidTranslationsToJson::class,
            ];

            foreach ($beforeCommands as $command) {
                $this->info("Running $command");
                Artisan::call($command);
            }

            $seeders = [
                \StepsTableSeeder::class,
                \ToolQuestionTypesTableSeeder::class,
                \SubStepTemplatesTableSeeder::class,
                \InputSourcesTableSeeder::class,
                \RoofTypesTableSeeder::class,
                \EnergyLabelsTableSeeder::class,
            ];

            foreach ($seeders as $seeder) {
                $this->info("Running $seeder");
                Artisan::call('db:seed', ['--class' => $seeder]);
            }

            $afterCommands = [
                AddQuestionsToDatabase::class,
                AddMasterInputSource::class,
            ];

            foreach ($afterCommands as $command) {
                $this->info("Running $command");
                Artisan::call($command);
            }

            //if ($this->confirm('Should we clear the cache ?', 'yes')) {
                $this->info('Cache cleared');
            //}
        } else {
            $this->info('K bye');
        }
    }
}
