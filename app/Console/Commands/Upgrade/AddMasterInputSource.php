<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Building;
use App\Services\BuildingDataCopyService;
use Illuminate\Console\Command;

class AddMasterInputSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:add-master-input-source {id?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensures each answer has a master input source';

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
        $ids = $this->argument('id');
        $buildings = empty($ids) ? Building::all() : Building::whereIn('id', $ids)->get();

        $this->info('Starting process to add/update master input source on all/given buildings...');
        $bar = $this->output->createProgressBar($buildings->count());
        $bar->start();

        foreach ($buildings as $building) {
            BuildingDataCopyService::setMasterInputSources($building);
            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
        $this->info('All done.');
    }
}
