<?php

namespace App\Helpers\Models;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;

class BuildingFeatureHelper
{
    public static function performSecondaryRoofTypeLogic(BuildingFeature $buildingFeature)
    {
        $masterInputSource = InputSource::master();
        $exampleBuildingInputSource = InputSource::exampleBuilding();

        // Master is handled by the getMyValuesTrait, and example building does not follow logic.
        if (! in_array($buildingFeature->input_source_id, [$masterInputSource->id, $exampleBuildingInputSource->id])) {
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
                            // There are roof types present. As of right now, there's 3 options. If the primary
                            // is not EXACTLY the one selected, we ensure it is. This is because if a user goes
                            // through the quick scan, the example building might set 2 secondary roof types purely
                            // to ensure values like surface are set (necessary for the calculations).
                            // However, this means the other one will not be deselected, causing confusion and
                            // incorrect calculations. Instead, we force to only select a single one. If they
                            // want to select a second one, they MUST go to the expert scan.

                            // NOTE: The expert scan uses the old saving method, which means it will first save the
                            // primary roof type (which triggers this observer) and then manually updates the
                            // building roof types. If the expert scan logic changes, we MUST alter
                            // the flow of this observer.
                            if ($secondaryRoofTypes->count() > 1 || $secondaryRoofTypes->where('roof_type_id', $roofTypeToLink->id)->count() === 0) {
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
                                ->forInputSource(InputSource::findByShort(InputSource::EXAMPLE_BUILDING_SHORT))
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
                                    ->forInputSource(InputSource::findByShort(InputSource::EXAMPLE_BUILDING_SHORT))
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

                                    // So, the measure application short comes from the example building. The data might be incorrect, in which
                                    // a pitched roof type measure is saved with a flat roof type. In that case, it's null, because the
                                    // measure exists, but isn't in the mapping. We won't do anything with it.
                                    if (! is_null($newShort)) {
                                        $measureApplication = MeasureApplication::findByShort($newShort);

                                        if ($measureApplication instanceof MeasureApplication) {
                                            $extra['measure_application_id'] = $measureApplication->id;
                                        }
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
}
