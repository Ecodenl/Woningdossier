<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\ExampleBuilding;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessApplyExampleBuilding implements ShouldQueue
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
        $exampleBuilding = $this->exampleBuilding;

        // Get buildings with this example building, with building features
        $buildings = Building::where('example_building_id', $exampleBuilding->id)
            ->with(['buildingFeatures' => function($query) {
                $query->allInputSources();
            }])
            ->get();

        foreach($buildings as $building)
        {
            // If building and building feature are valid, apply the example building via the job
            if ($building instanceof Building && $building->buildingFeatures instanceof BuildingFeature) {
                ApplyExampleBuilding::dispatch($exampleBuilding, $building, $building->buildingFeatures);
            }
        }
    }
}
