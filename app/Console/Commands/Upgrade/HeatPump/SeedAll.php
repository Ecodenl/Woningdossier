<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SeedAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:seed-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Just run the seeders for the upgrade, without all other mappings and migrations';

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
        Artisan::call('cache:clear');

        Artisan::call('db:seed', ['--class' => \ScansTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolLabelsTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolQuestionTypesTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \StepsTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \ToolQuestionsTableSeeder::class, '--force' => true]);
        DB::table('sub_steppables')->truncate();
        Artisan::call('db:seed', ['--class' => \SubSteppablesTableSeeder::class, '--force' => true]);

        Artisan::call('db:seed', ['--class' => \ElementsValuesTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \KeyFigureHeatPumpCoveragesTableSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \HeatPumpCharacteristicsTableSeeder::class, '--force' => true]);

        Artisan::call('cache:clear');
    }
}
