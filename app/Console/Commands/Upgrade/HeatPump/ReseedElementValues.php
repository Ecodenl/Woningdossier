<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReseedElementValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:reseed-elements-values';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-run the ElementsValuesTableSeeder';

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
        Artisan::call('db:seed', ['--class' => 'ElementsValuesTableSeeder', '--force' => true]);
    }
}
