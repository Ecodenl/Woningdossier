<?php

namespace App\Observers;

use App\Helpers\Models\BuildingFeatureHelper;
use App\Models\BuildingFeature;

class BuildingFeatureObserver
{
    public function saving(BuildingFeature $buildingFeature)
    {
        // Check if we need to manipulate the secondary roof types when the roof type ID is changed
        // We don't need to execute this if the building feature is from the master source. That will be handled by the
        // getMyValuesTrait
        if ($buildingFeature->isDirty('roof_type_id')) {
            BuildingFeatureHelper::performSecondaryRoofTypeLogic($buildingFeature);
        }
    }

    ####################
    ##
    ## I am just here to tell you this is not a good place for putting
    ## example building change detection as this will trigger an infinite loop.
    ##
    ####################
}
