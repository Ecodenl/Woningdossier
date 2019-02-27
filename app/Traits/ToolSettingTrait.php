<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Database\Eloquent\Model;

trait ToolSettingTrait
{
    /**
     * Returns an input source id if it's present on the model, we always try to get it from the current model since the example building is a input source as well.
     * However, if we cannot get it from the model we will try to get it from the session.
     *
     * @param Model $model
     *
     * @return int|null
     */
    public static function getChangedInputSourceId(Model $model)
    {
        $inputSourceId = null;

        // Try to obtain the input source from the model itself
        $inputSource = InputSource::find($model->input_source_id);

        // And override if necessary
        if ($inputSource instanceof InputSource) {
            $inputSourceId = $inputSource->id;
        } else if (\Auth::check()) {
            // Set the InputSource ID to the default of my input source
            $inputSourceId = HoomdossierSession::getInputSource();
            \Log::debug('Got the inputsource from session');
        } else {
            \Log::debug('ToolSettingTrait: $inputSource is not a instance. this means the input_source_id does not exist on the model and the trait is included in a wrong model !');
        }

        return $inputSourceId;
    }

    public static function bootToolSettingTrait()
    {
        // we set the has changed to true if the example_building_id was already filled and to false if its null.
        static::created(function (Model $model) {
            $building = Building::find(HoomdossierSession::getBuilding());

            $hasChanged = false;

            if ($building instanceof Building) {
                $hasChanged = $building->example_building_id === null ? false : true;
            }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (!is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });

        static::updated(function (Model $model) {
            $building = Building::find(HoomdossierSession::getBuilding());

            $hasChanged = false;

            if ($building instanceof Building) {
                $hasChanged = $building->example_building_id === null ? false : true;
            }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (!is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });
    }
}
