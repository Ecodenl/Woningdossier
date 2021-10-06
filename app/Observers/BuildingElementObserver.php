<?php

namespace App\Observers;

use App\Calculations\InsulatedGlazing;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Log;

class BuildingElementObserver
{
    public function saving(BuildingElement $buildingElement)
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $livingRoomsWindows = ToolQuestion::findByShort('living-rooms-windows');
        $sleepingRoomsWindows = ToolQuestion::findByShort('sleeping-rooms-windows');

        // Check if we need to manipulate the insulated glazings. There's 2 elements that are relevant, so we check
        // if it's either of that element, and then if the value is dirty, we can continue
        // We don't need to execute this if the building element is from the master source. That will be handled by the
        // getMyValuesTrait
        if (($buildingElement->element_id === $livingRoomsWindows->id || $buildingElement->element_id === $sleepingRoomsWindows->id)
            && $buildingElement->isDirty('element_value_id') && $buildingElement->input_source_id != $masterInputSource->id
        ) {
            if (($building = $buildingElement->building) instanceof Building) {
                $currentInputSource = HoomdossierSession::getInputSource(true);

                // Let's see which insulated glazing we need to apply
                if (($elementValue = $buildingElement->elementValue) instanceof ElementValue) {
                    // TODO: Check other element value, we need to grab the LOWEST comfort
                    $comfort = $elementValue->configurations['comfort'] ?? 0;

                    if (($singlePaneGlass = InsulatedGlazing::where('name->nl', 'Enkelglas')->first()) instanceof InsulatedGlazing) {
                        // This assumes there's no other insulated glazings, which there are not currently
                        if (($doublePaneGlass = InsulatedGlazing::where('id', '!=', $singlePaneGlass->id)->first()) instanceof InsulatedGlazing) {
                            // If comfort is below 3, then it's single pane glass. If it's higher/equal to 3, then it's
                            // double pane glass.
                            $glassToApply = $comfort < 3 ? $singlePaneGlass : $doublePaneGlass;

                            // Get all the building insulated glazings which don't already have this glass type selected
                            $insulatedGlazings = $building->currentInsulatedGlazing()
                                ->forInputSource($currentInputSource)
                                ->where('insulated_glazing_id', '!=', $glassToApply->id)
                                ->get();

                            // TODO: Apply the glass to the glazings


                        }
                    } else {
                        Log::alert('Insulated glazing name changed for "Enkelglas"!');
                    }
                }
            }
        }
    }
}
