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
     * @param  int  $buildingId
     * @param  int  $changedInputSourceId  | The input source id that changed something
     * @param  bool  $hasChanged
     */
    public static function setChanged(
        int $buildingId,
        int $changedInputSourceId,
        bool $hasChanged
    ) {
        $changed = $hasChanged ? "true" : "false";
        \Log::debug(__METHOD__." (source: ".$changedInputSourceId.") to ".$changed." for building ".$buildingId);

        // get all existing input sources
        $inputSources = InputSource::all();

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
     *
     * @param  Building  $building
     */
    public static function clearChanged(Building $building)
    {
        ToolSetting::withoutGlobalScope(GetValueScope::class)
                   ->where('building_id', '=', $building->id)
                   ->where('has_changed', '=', true)
                   ->update(['has_changed' => false]);
    }
}
