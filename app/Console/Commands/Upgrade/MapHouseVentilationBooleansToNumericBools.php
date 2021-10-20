<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\ToolQuestionAnswer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapHouseVentilationBooleansToNumericBools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-house-ventilation-booleans-to-numeric-bools';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map true or false to 1 or 2, for consistency reasons.';

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
        $houseVentilationService = Service::findByShort('house-ventilation');

        // here we will map the true or false answers to a 1 or 0, so remain somewhat consistent.
        $buildingServices = BuildingService::withoutGlobalScopes()
            ->where('service_id', $houseVentilationService->id)
            ->cursor();

        $bar = $this->output->createProgressBar($buildingServices->count());
        foreach ($buildingServices as $buildingService) {

            $oldExtra = $buildingService->extra;
            $extra = $buildingService->extra;

            if (isset($extra['demand_driven']) && ($extra['demand_driven'] === true || $extra['demand_driven'] === "true")) {
                $extra['demand_driven'] = 1;
            }

            if (isset($extra['demand_driven']) && ($extra['demand_driven'] === false || $extra['demand_driven'] === "false")) {
                $extra['demand_driven'] = 0;
            }

            if (isset($extra['heat_recovery']) && ($extra['heat_recovery'] === true || $extra['heat_recovery'] === "true")) {
                $extra['heat_recovery'] = 1;
            }

            if (isset($extra['heat_recovery']) && ($extra['heat_recovery'] === false || $extra['heat_recovery'] === "false")) {
                $extra['heat_recovery'] = 0;
            }


            $buildingService->extra = $extra;

            // so we dont do a enormous amount of queries.
            if ($extra !== $oldExtra) {
                $buildingService->save();

            }
            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }
}
