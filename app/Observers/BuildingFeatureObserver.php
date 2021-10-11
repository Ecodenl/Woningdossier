<?php

namespace App\Observers;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\BuildingType;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\ToolQuestion;
use App\Services\ExampleBuildingService;
use Illuminate\Support\Facades\Log;

class BuildingFeatureObserver
{
    public function saving(BuildingFeature $buildingFeature)
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Check if we need to manipulate the secondary roof types when the roof type id is changed
        // We don't need to execute this if the building feature is from the master source. That will be handled by the
        // getMyValuesTrait
        if ($buildingFeature->isDirty('roof_type_id') && $buildingFeature->input_source_id != $masterInputSource->id) {
            if (($building = $buildingFeature->building) instanceof Building) {
                if (($primaryRoofType = $buildingFeature->roofType) instanceof RoofType) {
                    $currentInputSource = $buildingFeature->inputSource;
                    $secondaryRoofTypes = $building->roofTypes()->forInputSource($currentInputSource)->get();

                    if (($roofTypeToLink = RoofType::findByShort(RoofType::PRIMARY_TO_SECONDARY_MAP[$primaryRoofType->short])) instanceof RoofType) {
                        $shouldCreate = false;

                        if ($secondaryRoofTypes->count() === 0) {
                            // No roof types defined yet. Let's set them.
                            $shouldCreate = true;
                        } else {
                            // There are roof types present. We need to check if the primary one is included. If it's not,
                            // we delete the other ones, and then built a new one.
                            if ($secondaryRoofTypes->where('roof_type_id', $roofTypeToLink->id)->count() === 0) {
                                // It's not present.

                                // Delete old ones
                                foreach ($secondaryRoofTypes as $secondaryRoofType) {
                                    $secondaryRoofType->delete();
                                }

                                // Enable creating a new one
                                $shouldCreate = true;
                            }
                        }

                        if ($shouldCreate) {
                            $exampleBuildingRoofType = $building->roofTypes()
                                ->where('roof_type_id', $roofTypeToLink->id)
                                ->forInputSource(InputSource::findByShort(InputSource::EXAMPLE_BUILDING))
                                ->first();

                            if (! $exampleBuildingRoofType instanceof BuildingRoofType) {
                                // No data set, we need the other roof type. We unset none and the linked short
                                $shorts = array_flip(RoofType::SECONDARY_ROOF_TYPE_SHORTS);
                                unset($shorts['none']);
                                unset($shorts[$roofTypeToLink->short]);
                                $short = array_key_first($shorts);
                                $otherRoofType = RoofType::findByShort($short);

                                $exampleBuildingRoofType = $building->roofTypes()
                                    ->where('roof_type_id', $otherRoofType->id)
                                    ->forInputSource(InputSource::findByShort(InputSource::EXAMPLE_BUILDING))
                                    ->first();
                            }

                            if ($exampleBuildingRoofType instanceof BuildingRoofType) {
                                $exampleBuildingRoofType->replicate()
                                    ->fill([
                                        'input_source_id' => $currentInputSource->id,
                                        'roof_type_id' => $roofTypeToLink->id,
                                    ])
                                    ->save();
                            } else {
                                // No example building data, we just built a new one
                                $building->roofTypes()->create([
                                    'roof_type_id' => $roofTypeToLink->id, 'input_source_id' => $currentInputSource->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    ####################
    ##
    ## I am just here to tell you this is not a good place for putting
    ## example building change detection as this will trigger an infinite loop.
    ##
    ####################

//    public function saved(BuildingFeature $buildingFeature)
//    {
//        if ($buildingFeature->inputSource->short == InputSource::EXAMPLE_BUILDING){
//            // No example building actions should be triggered for the example building itself.
//            return;
//        }
//        if ($buildingFeature->isDirty(['building_type_id', 'build_year'])) {
//            Log::debug(__METHOD__ . " Either building_type_id, build_year (or combination) has changed");
//                // either building_type_id or build_year was changed (or both)
//                $exampleBuilding = $this->getExampleBuildingIfChangeIsNeeded($buildingFeature, $buildingFeature->example_building_id);
//
//                $building = $buildingFeature->building;
//
//            if ($exampleBuilding instanceof ExampleBuilding){
//                if ($building->exampleBuilding->id !== $exampleBuilding->id) {
//                    Log::debug(
//                        __METHOD__." Setting new example building to ".$exampleBuilding->id
//                    );
//                    $buildingFeature->building->exampleBuilding()->associate(
//                        $exampleBuilding
//                    )->save();
//                    // And we let the BuildingObserver handle the rest.
//                }
//                else {
//                    // example building id doesn't change, which wouldn't trigger the dirty of the building observer.
//                    // this probably means the example building content is different because of the year, but the example building ID itself is not.
//                    Log::debug(__METHOD__ . " Triggering example building myself.");
//                    ExampleBuildingService::apply(
//                        $exampleBuilding,
//                        $buildingFeature->build_year,
//                        $building
//                    );
//                    // if it's the first time, also fill the master input source.
//                    if ($this->isFirstTimeToolIsFilled($building)) {
//                        ExampleBuildingService::apply(
//                            $exampleBuilding,
//                            $buildingFeature->build_year,
//                            $buildingFeature->building,
//                            InputSource::findByShort(InputSource::MASTER_SHORT)
//                        );
//                    }
//
//                }
//            } else {
//                //Log::debug(__METHOD__ . " No example building was found. Clearing the example building for building " . $buildingFeature->building->id);
//                //ExampleBuildingService::clearExampleBuilding($buildingFeature->building);
//                $buildingFeature->building->exampleBuilding()->dissociate()->save();
//            }
//        }
//    }

//    private function getExampleBuildingIfChangeIsNeeded(BuildingFeature $buildingFeature, $currentExampleBuildingId) : ?ExampleBuilding
//    {
//        $buildingTypeId = $buildingFeature->building_type_id;
//        $buildingType    = BuildingType::find($buildingTypeId);
//        if (!$buildingType instanceof BuildingType){
//            return null;
//        }
//        $exampleBuilding = ExampleBuilding::generic()->where(
//            'building_type_id',
//            $buildingType->id
//        )->first();
//
//        if (!$exampleBuilding instanceof ExampleBuilding){
//            // No example building, so can't change then.
//            return null;
//        }
//
//        if ($exampleBuilding->id !== $currentExampleBuildingId){
//            // We know the change is sure
//            return $exampleBuilding;
//        }
//
//        if ($buildingFeature->isDirty('build_year')){
//            $old = (int) $buildingFeature->getOriginal('build_year');
//            $new = (int) $buildingFeature->build_year;
//            // if the build_year is dirty:
//            // check the combination of example_building_id with new build_year
//            // against the combination of example_building_id with old build_year
//            $oldContents = $exampleBuilding->getContentForYear($old);
//            $newContents = $exampleBuilding->getContentForYear($new);
//
//            if ($oldContents->id !== $newContents->id) {
//                return $exampleBuilding;
//            }
//        }
//
//        return null;
//    }

//    private function isFirstTimeToolIsFilled(Building $building)
//    {
//        $inputSource      = InputSource::findByShort(InputSource::MASTER_SHORT);
//        $cookTypeQuestion = ToolQuestion::findByShort('cook-type');
//
//        return is_null($building->getAnswer($inputSource, $cookTypeQuestion));
//    }
}
