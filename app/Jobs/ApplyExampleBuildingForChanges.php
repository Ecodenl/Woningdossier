<?php

namespace App\Jobs;

use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ExampleBuildingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplyExampleBuildingForChanges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $changes;
    public BuildingFeature $buildingFeature;

    public function __construct(BuildingFeature $buildingFeature, array $changes)
    {
        $this->changes = $changes;
        $this->buildingFeature = $buildingFeature;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exampleBuilding = $this->getExampleBuildingIfChangeIsNeeded($this->changes);

        if ($exampleBuilding instanceof ExampleBuilding) {
            Log::debug("Example building should be (re)applied!");
        } else {
            Log::debug("No change in example building contents");
        }

        if ($exampleBuilding instanceof ExampleBuilding) {
            // Apply the example building
            $this->retriggerExampleBuildingApplication($exampleBuilding);
        }
    }

    protected function getExampleBuildingIfChangeIsNeeded(array $changes): ?ExampleBuilding
    {
        // objects for first checks
        $buildingFeature = $this->buildingFeature;

        // We need this to do stuff
        if ($buildingFeature instanceof BuildingFeature && !is_null($buildingFeature->build_year)) {
            // current values for comparison later on
            $currentExampleBuildingId = $this->building->example_building_id;
            $currentBuildYearValue = (int)$buildingFeature->build_year;

            if (array_key_exists('building_type_id', $changes)) {
                $buildingType = BuildingType::find((int)$changes['building_type_id']);
            } else {
                $buildingType = $buildingFeature->buildingType;
            }

            if (!$buildingType instanceof BuildingType) {
                return null;
            }

            $exampleBuilding = ExampleBuilding::generic()->where(
                'building_type_id',
                $buildingType->id
            )->first();

            if (!$exampleBuilding instanceof ExampleBuilding) {
                // No example building, so can't change then.
                return null;
            }

            if ($exampleBuilding->id !== $currentExampleBuildingId) {
                // We know the change is sure
                return $exampleBuilding;
            }

            if (array_key_exists('build_year', $changes)) {
                $new = (int)$changes['build_year'];
                if ($currentBuildYearValue === $new) {
                    return null;
                }

                // if the build_year is dirty:
                // check the combination of example_building_id with new build_year
                // against the combination of example_building_id with old build_year
                $oldContents = $exampleBuilding->getContentForYear($currentBuildYearValue);
                $newContents = $exampleBuilding->getContentForYear($new);

                if ($oldContents instanceof ExampleBuildingContent) {
                    if ($oldContents->id !== $newContents->id) {
                        return $exampleBuilding;
                    }
                } else {
                    return $exampleBuilding;
                }

            }
        } else {
            Log::debug("Building feature undefined, or build year not set for building {$this->building->id}");
        }

        return null;
    }

    private function retriggerExampleBuildingApplication(ExampleBuilding $exampleBuilding)
    {
        Log::debug(__METHOD__);
        if ($this->building->example_building_id !== $exampleBuilding->id) {
            Log::debug("Example building ID changes (" . $this->building->example_building_id . " -> " . $exampleBuilding->id . ")");
            // change example building, let the observer do the rest
            $this->building->exampleBuilding()->associate($exampleBuilding)->save();
        }

        // For the example building input source it could be that the build year isn't set.
        // If so we use the build_year from the current input source
        //$buildYear = $this->building->buildingFeatures->build_year ?? $this->building->buildingFeatures()->forInputSource($this->currentInputSource)->first()->build_year;
        $buildYear = $this->building->buildingFeatures()->forInputSource($this->currentInputSource)->first()->build_year;

        // manually trigger
        ExampleBuildingService::apply(
            $exampleBuilding,
            $buildYear,
            $this->building
        );

        $exampleBuildingShouldOverrideUserData = $this->building
                ->completedSubSteps()
                ->forInputSource(InputSource::findByShort(InputSource::MASTER_SHORT))
                ->where('sub_step_id', '>', 3)
                ->count() <= 0;

        if ($exampleBuildingShouldOverrideUserData) {
            ExampleBuildingService::apply(
                $exampleBuilding,
                //$this->building->buildingFeatures->build_year,
                $buildYear,
                $this->building,
                $this->currentInputSource
            );
        }
    }
}
