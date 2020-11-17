<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolSetting;
use App\Scopes\GetValueScope;

class ToolSettingService
{
    /**
     * Set the changed status.
     *
     * @param int $changedInputSourceId | The input source id that changed something
     */
    public static function setChanged(int $buildingId, int $changedInputSourceId, bool $hasChanged) {
        // if a example building get applied the input source = example-building
        // If a user changes the example building we dont to notify the all the other input-sources / users from this
        // so we only set the changed for the current input source.
        // else, we just notify all the input sources
        if ($changedInputSourceId == InputSource::findByShort('example-building')->id) {
            ToolSetting::withoutGlobalScope(GetValueScope::class)
                       ->updateOrCreate(
                           [
                               'building_id'             => $buildingId,
                               'input_source_id'         => HoomdossierSession::getInputSource(),
                               'changed_input_source_id' => $changedInputSourceId,
                           ],
                           [
                               'has_changed' => $hasChanged,
                           ]
                       );
        } else {
            // Notify all other input sources
            $inputSources = \App\Helpers\Cache\InputSource::getOrdered();

            foreach ($inputSources as $inputSource) {
                ToolSetting::withoutGlobalScope(GetValueScope::class)
                           ->updateOrCreate(
                               [
                                   'building_id'             => $buildingId,
                                   'input_source_id'         => $inputSource->id,
                                   'changed_input_source_id' => $changedInputSourceId,
                               ],
                               [
                                   'has_changed' => $hasChanged,
                               ]
                           );
            }
        }
    }

    /**
     * Clear all changed notifications for a building.
     */
    public static function clearChanged(Building $building)
    {
        ToolSetting::withoutGlobalScope(GetValueScope::class)
                   ->where('building_id', '=', $building->id)
                   ->where('has_changed', '=', true)
                   ->update(['has_changed' => false]);
    }
}
