<?php

namespace App\Observers;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\InsulatingGlazing;
use Illuminate\Support\Facades\Log;

class BuildingElementObserver
{
    public function saving(BuildingElement $buildingElement)
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // We need the building for both situations, so we check it first
        // We don't need to execute this if the building element is from the master source. That will be handled by the
        // getMyValuesTrait
        if (($building = $buildingElement->building) instanceof Building
            && $buildingElement->input_source_id != $masterInputSource->id
        ) {
            $currentInputSource = HoomdossierSession::getInputSource(true);

            ## Insulating Glazing
            $livingRoomsWindows = Element::findByShort('living-rooms-windows');
            $sleepingRoomsWindows = Element::findByShort('sleeping-rooms-windows');

            // Check if we need to manipulate the insulated glazings. There's 2 elements that are relevant, so we check
            // if it's either of those elements, and then if the value is dirty, we can continue
            if (($buildingElement->element_id === $livingRoomsWindows->id || $buildingElement->element_id === $sleepingRoomsWindows->id)
                && $buildingElement->isDirty('element_value_id')
            ) {
                // Let's see which insulated glazing we need to apply
                if (($elementValue = $buildingElement->elementValue) instanceof ElementValue) {
                    $comfort = $elementValue->configurations['comfort'] ?? 0;

                    // We need to check the lowest glass type, so we need to check the other element value also
                    $elementToCompare = $buildingElement->element_id === $livingRoomsWindows->id
                        ? $livingRoomsWindows : $sleepingRoomsWindows;

                    $otherElementValue = $building->buildingElements()
                        ->where('element_id', $elementToCompare->id)
                        ->forInputSource($currentInputSource)
                        ->first();

                    // Get the lowest comfort score
                    if ($otherElementValue instanceof ElementValue) {
                        $comfort = min($comfort, ($otherElementValue->configurations['comfort'] ?? 0));
                    }

                    if (($singlePaneGlass = InsulatingGlazing::where('name->nl', 'Enkelglas')->first())
                        instanceof InsulatingGlazing
                    ) {
                        // This assumes there's no other insulated glazings, which there are not currently
                        if (($doublePaneGlass = InsulatingGlazing::where('id', '!=', $singlePaneGlass->id)->first())
                            instanceof InsulatingGlazing
                        ) {
                            // If comfort is below 3, then it's single pane glass. If it's higher/equal to 3, then it's
                            // double pane glass.
                            $glassToApply = $comfort < 3 ? $singlePaneGlass : $doublePaneGlass;

                            // Get all the building insulated glazings which don't already have this glass type selected
                            $insulatedGlazings = $building->currentInsulatedGlazing()
                                ->forInputSource($currentInputSource)
                                ->where('insulating_glazing_id', '!=', $glassToApply->id)
                                ->get();

                            // Update them with the new glass type
                            foreach ($insulatedGlazings as $insulatedGlazing) {
                                $insulatedGlazing->update([
                                    'insulating_glazing_id' => $glassToApply->id,
                                ]);
                            }
                        }
                    } else {
                        Log::alert('Insulated glazing name changed for "Enkelglas"!');
                    }
                }
            }

            ## Roof insulation
            $roofInsulation = Element::findByShort('roof-insulation');

            // Check if we need to manipulate the roof insulation. Same logic as above applied
            if ($buildingElement->element_id === $roofInsulation->id
                && $buildingElement->isDirty('element_value_id')
            ) {
                // Get all roof types that don't have this insulation
                $roofTypesToUpdate = $building->roofTypes()
                    ->forInputSource($currentInputSource)
                    ->where('element_value_id', '!=', $buildingElement->element_value_id)
                    ->get();

                foreach ($roofTypesToUpdate as $roofTypeToUpdate) {
                    $roofTypeToUpdate->update([
                        'element_value_id' => $buildingElement->element_value_id,
                    ]);
                }
            }
        }
    }
}
