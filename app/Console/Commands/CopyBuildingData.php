<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\InputSource;
use App\Services\BuildingDataCopyService;
use Illuminate\Console\Command;

class CopyBuildingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:building-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to copy building data, to quickly test allot of buildings';

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
        if (app()->environment() === "local") {

            $buildings = Building::all();
            $residentInputSource = InputSource::findByShort('resident');
            $coachInputSource = InputSource::findByShort('coach');

            $bar = $this->output->createProgressBar($buildings->count());
            $bar->start();
            foreach ($buildings as $building) {
                $bar->advance(1);
                BuildingDataCopyService::copy($building, $residentInputSource, $coachInputSource);
            }
            $bar->finish();
        }
    }
}
