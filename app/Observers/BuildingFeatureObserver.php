<?php

namespace App\Observers;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;

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
                                $extra = $exampleBuildingRoofType->extra;
                                $measureApplicationId = $extra['measure_application_id'] ?? null;
                                $measureApplication = MeasureApplication::find($measureApplicationId);

                                // Check the measure application from the extra data to ensure we set the correct
                                // measure
                                if ($measureApplication instanceof MeasureApplication) {
                                    // We check the old measure short with the new roof type to link,
                                    // which will give us the map to a potential new measure
                                    $newShort = RoofType::MEASURE_MAP[$roofTypeToLink->short][$measureApplication->short] ?? null;

                                    $measureApplication = MeasureApplication::findByShort($newShort);

                                    if ($measureApplication instanceof MeasureApplication) {
                                        $extra['measure_application_id'] = $measureApplication->id;
                                    }
                                }

                                $exampleBuildingRoofType->replicate()
                                    ->fill([
                                        'input_source_id' => $currentInputSource->id,
                                        'roof_type_id' => $roofTypeToLink->id,
                                        'extra' => $extra,
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
}
