<?php

namespace App\Console\Commands\Tool;

use App\Models\Building;
use App\Services\Models\BuildingService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CheckMunicipalityLessBuildings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:check-municipality-less-buildings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check ALL buildings without a municipality and try to attach them again.';

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
     * @return int
     */
    public function handle()
    {
        Building::whereNull('municipality_id')
            ->limit(10)
            ->chunkById(100, function (Collection $buildings) {
                /** @var Building $building */
                foreach ($buildings as $building) {
                    $buildingService = BuildingService::init($building);
                    $buildingService->updateAddress($building->toArray());
                    $buildingService->attachMunicipality();
                }
            });

        return 0;
    }
}
