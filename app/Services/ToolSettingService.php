<?php

namespace App\Services;

use App\Models\ToolSetting;

class ToolSettingService
{
    /**
     * Set the changed status.
     *
     * @param int  $buildingId
     * @param int  $inputSourceId
     * @param bool $hasChanged
     */
    public static function setChanged(int $buildingId, int $inputSourceId, bool $hasChanged)
    {
        $toolSetting = ToolSetting::where('building_id', $buildingId)
            ->where('changed_input_source_id', $inputSourceId)->first();

        // update the has changed column or create a new one.
        if ($toolSetting instanceof ToolSetting) {
            $toolSetting->has_changed = $hasChanged;
            $toolSetting->save();
        } else {
            ToolSetting::create(
                [
                    'building_id' => $buildingId,
                    'changed_input_source_id' => $inputSourceId,
                    'has_changed' => $hasChanged,
                ]
            );
        }
    }
}
