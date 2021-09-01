<?php

namespace App\Observers;

use App\Models\BuildingFeature;
use App\Models\BuildingType;
use App\Models\ExampleBuilding;
use Illuminate\Support\Facades\Log;

class BuildingFeatureObserver
{
    public function saved(BuildingFeature $buildingFeature)
    {
        if ($buildingFeature->isDirty(['building_type_id', 'build_year'])) {
            Log::debug(__METHOD__ . " Either building_type_id, build_year (or combination) has changed");
                // either building_type_id or build_year was changed (or both)
                $exampleBuilding = $this->getExampleBuildingIfChangeIsNeeded($buildingFeature, $buildingFeature->example_building_id);

            if ($exampleBuilding instanceof ExampleBuilding){
                Log::debug(__METHOD__. " Setting new example building to " . $exampleBuilding->id);
                $buildingFeature->building->exampleBuilding()->associate($exampleBuilding)->save();
                // And we let the BuildingObserver handle the rest.
            } else {
                //Log::debug(__METHOD__ . " No example building was found. Clearing the example building for building " . $buildingFeature->building->id);
                //ExampleBuildingService::clearExampleBuilding($buildingFeature->building);
                $buildingFeature->building->exampleBuilding()->dissociate()->save();

            }
        }
    }

    private function getExampleBuildingIfChangeIsNeeded(BuildingFeature $buildingFeature, $currentExampleBuildingId) : ?ExampleBuilding
    {
        $buildingTypeId = $buildingFeature->building_type_id;
        $buildingType    = BuildingType::find($buildingTypeId);
        if (!$buildingType instanceof BuildingType){
            return null;
        }
        $exampleBuilding = ExampleBuilding::generic()->where(
            'building_type_id',
            $buildingType->id
        )->first();

        if (!$exampleBuilding instanceof ExampleBuilding){
            // No example building, so can't change then.
            return null;
        }

        if ($exampleBuilding->id !== $currentExampleBuildingId){
            // We know the change is sure
            return $exampleBuilding;
        }

        if ($buildingFeature->isDirty('build_year')){
            $old = (int) $buildingFeature->getOriginal('build_year');
            $new = (int) $buildingFeature->build_year;
            // if the build_year is dirty:
            // check the combination of example_building_id with new build_year
            // against the combination of example_building_id with old build_year
            $oldContents = $exampleBuilding->getContentForYear($old);
            $newContents = $exampleBuilding->getContentForYear($new);

            if ($oldContents->id !== $newContents->id) {
                return $exampleBuilding;
            }
        }

        return null;
    }

}
