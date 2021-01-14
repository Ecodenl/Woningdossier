<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\ExampleBuilding;
use App\Services\ExampleBuildingService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ApplyExampleBuilding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exampleBuilding;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExampleBuilding $exampleBuilding)
    {
        $this->exampleBuilding = $exampleBuilding;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug('dispatched');
        $exampleBuilding = $this->exampleBuilding;

        $buildings = Building::where('example_building_id', $exampleBuilding->id)
            ->with(['buildingFeatures' => function($query) {
                $query->allInputSources();
            }])
            ->get();

        foreach($buildings as $building)
        {
            if ($building instanceof Building && $building->buildingFeatures instanceof BuildingFeature) {
                ExampleBuildingService::apply($exampleBuilding, $building->buildingFeatures->build_year, $building);
            }
        }
    }
}
