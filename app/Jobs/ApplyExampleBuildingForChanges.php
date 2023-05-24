<?php

namespace App\Jobs;

use App\Helpers\ExampleBuildingHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ExampleBuildingService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplyExampleBuildingForChanges extends NonHandleableJobAfterReset
{
//    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $changes;
    public Building $building;
    public BuildingFeature $buildingFeature;
    public InputSource $masterInputSource;
    public InputSource $applyForInputSource;

    public function __construct(BuildingFeature $buildingFeature, array $changes, InputSource $applyForInputSource)
    {
        $this->changes = $changes;
        $this->buildingFeature = $buildingFeature;
        $this->building = $buildingFeature->building;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->applyForInputSource = $applyForInputSource;
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
            Log::debug(__CLASS__." Example building should be (re)applied!");
            // Apply the example building
            $this->retriggerExampleBuildingApplication($exampleBuilding);
        } else {
            Log::debug(__CLASS__." No change in example building contents, or no new contents found.");
        }
    }

    protected function getExampleBuildingIfChangeIsNeeded(array $changes): ?ExampleBuilding
    {
        // objects for first checks
        $buildingFeature = $this->buildingFeature;
        $currentExampleBuildingId = $buildingFeature->example_building_id;

        // Kinda obvious but still
        // if the user changed his example building in the frontend we will just apply that one.
        if (array_key_exists('example_building_id', $changes)) {

            // to prevent ANOTHER apply being executed, with 0 purpose.
            if ($changes['example_building_id'] != $currentExampleBuildingId) {
                return ExampleBuilding::find($changes['example_building_id']);
            }
            // since the example_building_id is passed on there is no need to check the logic further down
            return null;
        }

        $currentBuildYearValue = $buildingFeature->build_year;
        $changedBuildYear = $changes['build_year'] ?? null;

        // We need this to do stuff
        if (! is_null($currentBuildYearValue) || !is_null($changedBuildYear)) {
            // current values for comparison later on
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
                // No example building for the building type, so can't change then.
                return null;
            }

            if ($exampleBuilding->id !== $currentExampleBuildingId) {
                // We know the change is sure
                return $exampleBuilding;
            }

            if (array_key_exists('build_year', $changes)) {
                // when there is no change in the given build year there is no point in applying it again.
                if ($currentBuildYearValue === $changedBuildYear) {
                    return null;
                }

                // there is a last resort for (mostly) old buildings.
                if ($this->getExampleBuildingIfContentChanged($exampleBuilding, $currentBuildYearValue, $changedBuildYear) instanceof ExampleBuilding) {
                    return $exampleBuilding;
                }

                // a last resort to check if there is some content, could be that the user has a building which is below
                // the lowest available example building content its build year.
                $contentForLowestBuildYear = $exampleBuilding->contents()->orderBy('build_year')->first();
                if ($changedBuildYear < $contentForLowestBuildYear->build_year) {
                    return $this->getExampleBuildingIfContentChanged($exampleBuilding, $currentBuildYearValue, $contentForLowestBuildYear->build_year);
                }

            }
        } else {
            Log::debug(__CLASS__." Build year not set for building {$this->building->id}");
        }

        return null;
    }

    private function getExampleBuildingIfContentChanged(ExampleBuilding $exampleBuilding, $currentBuildYearValue, $changedBuildYear): ?ExampleBuilding
    {
        // if the build_year is dirty:
        // check the combination of example_building_id with new build_year
        // against the combination of example_building_id with old build_year
        $oldContents = $exampleBuilding->getContentForYear($currentBuildYearValue);
        $newContents = $exampleBuilding->getContentForYear($changedBuildYear);

        if ($newContents instanceof ExampleBuildingContent) {
            if ($oldContents instanceof ExampleBuildingContent) {
                // old content exists, check if the new one actually differs.
                if ($oldContents->id !== $newContents->id) {
                    return $exampleBuilding;
                }
            } else {
                // no old content, so its the first time a example building is applied.
                return $exampleBuilding;
            }
        }
        return null;
    }

    private function retriggerExampleBuildingApplication(ExampleBuilding $exampleBuilding)
    {
        Log::debug(__METHOD__);
        $buildingFeature =  $this->building->buildingFeatures()->forInputSource($this->masterInputSource)->first();
        if ($buildingFeature->example_building_id !== $exampleBuilding->id) {

            Log::debug(__CLASS__." Example building ID changes (" . $buildingFeature->example_building_id . " -> " . $exampleBuilding->id . ")");
            $buildingFeatureToUpdate = $this->building->buildingFeatures()->forInputSource($this->applyForInputSource)->first();

            if ($buildingFeatureToUpdate instanceof BuildingFeature) {
                $buildingFeatureToUpdate->update(['example_building_id' => $exampleBuilding->id]);
            }
        }

        // more of a fallback
        $buildYear = $buildingFeature->build_year;

        // so this could happen on old legacy accounts where there is no build year available on the master or current input source
        // that case we will try to pull a build year from any other input source as this is more credible than guessing anything.
        if (is_null($buildYear)) {
            $buildYear = $this->building->buildingFeatures()->allInputSources()->whereNotNull('build_year')->pluck('build_year')->first();
            // it can still be empty, if so we will just set the current year as we have nothing to rely on.
            if (is_null($buildYear)) {
                $buildYear = date('Y');
            }
        }

        if (array_key_exists('build_year', $this->changes)) {
            $buildYear = $this->changes['build_year'];
        }

        // alter the buildYear when needed (extra business logic)
        // when the user filled in a build year thats lower than the lowest available example building content
        // just use the lowest available one.
        $contentForLowestBuildYear = $exampleBuilding->contents()->orderBy('build_year')->first();
        if ($buildYear < $contentForLowestBuildYear->build_year) {
            Log::debug("Building id: {$this->building->id} filled in build year: {$buildYear} altered to lowest available build year {$contentForLowestBuildYear->build_year}");
            $buildYear = $contentForLowestBuildYear->build_year;
        }

        // We apply the example building only if the user has not proceeded further than the example building
        // sub steps. We simply check if the user has completed any sub step _besides_ the example building sub steps
        $totalOtherCompletedSubSteps = $this->building->completedSubSteps()
            ->forInputSource($this->masterInputSource)
            ->leftJoin('sub_steps', 'completed_sub_steps.sub_step_id', '=', 'sub_steps.id')
            ->whereNotIn('sub_steps.slug->nl', ExampleBuildingHelper::RELEVANT_SUB_STEPS)
            ->count();

        if ($totalOtherCompletedSubSteps === 0) {
            Log::debug(__CLASS__ . ' Override user data with example building data. $totalOtherCompletedSubSteps: ' . $totalOtherCompletedSubSteps);
            ExampleBuildingService::apply(
                $exampleBuilding,
                $buildYear,
                $this->building,
                $this->applyForInputSource
            );
        }

        // Manually trigger. We want to trigger the example building sourced later, as otherwise the "last changed
        // source" is the $applyForInputSource instead of the example building.
        ExampleBuildingService::apply(
            $exampleBuilding,
            $buildYear,
            $this->building,
            InputSource::findByShort(InputSource::EXAMPLE_BUILDING),
        );
    }
}
