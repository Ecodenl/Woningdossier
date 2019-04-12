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
        } elseif (\Auth::check()) {
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
            // $building = Building::find(HoomdossierSession::getBuilding());

            $hasChanged = true;
            dd($hasChanged);

            // this was a requested feature so no alert would be triggered after the first page was done
            // i'll just comment this out cause my sixth sense tell;s me this will be requested again.
            //
            //  if ($building instanceof Building) {
            //     $hasChanged = null === $building->example_building_id ? false : true;
            //  }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (! is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });

        static::updated(function (Model $model) {
            // $building = Building::find(HoomdossierSession::getBuilding());

            $hasChanged = true;

            // this was a requested feature so no alert would be triggered after the first page was done
            // i'll just comment this out cause my sixth sense tell;s me this will be requested again.
            // if ($building instanceof Building) {
            //     $hasChanged = null === $building->example_building_id ? false : true;
            // }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (! is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });
    }
}
