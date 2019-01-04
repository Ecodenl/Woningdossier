<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\ToolSetting;
use Illuminate\Database\Eloquent\Model;

class ToolSettingService
{

    public static function setChanged($buildingId)
    {
        $hasChanged = false;
        $args = func_get_args();

        // filter the models out off all arguments
        $models = collect($args)->filter(function ($value) {
            return $value instanceof Model;
        });

        foreach ($models as $model) {
            // todo this does not work well.
            if ($model->wasChanged()) {
                $hasChanged = true;
            }
        }

        $toolSetting = ToolSetting::where('building_id', $buildingId)
            ->where('changed_input_source_id', HoomdossierSession::getInputSource())->first();

        if ($toolSetting instanceof ToolSetting) {
            dd($hasChanged);
            $toolSetting->has_changed = $hasChanged;
            $toolSetting->save();
        } else {
            ToolSetting::create(
                [
                    'building_id' => $buildingId,
                    'changed_input_source_id' => HoomdossierSession::getInputSource(),
                    'has_changed' => $hasChanged
                ]
            );
        }
    }
}