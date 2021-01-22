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
    protected $building;
    protected $buildingFeature;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExampleBuilding $exampleBuilding, Building $building, BuildingFeature $buildingFeature)
    {
        $this->exampleBuilding = $exampleBuilding;
        $this->building = $building;
        $this->buildingFeature = $buildingFeature;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // We apply the example building with the given data
        ExampleBuildingService::apply($this->exampleBuilding, $this->buildingFeature->build_year, $this->building);
    }
}
