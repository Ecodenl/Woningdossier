<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Database\Eloquent\Model;

trait ToolSettingTrait
{
    /**
     * Returns an input source ID if it's present on the model or in the session.
     * There, however, is a case when this method can return null: on
     * registration as the user is not logged in (yet) and the model has no
     * input source just yet.
     *
     * @param Model $model
     *
     * @return int|null
     */
    public static function getInputSourceId(Model $model)
    {
        // Try to obtain the input source from the model itself
        $inputSource = InputSource::find($model->input_source_id);

        // Set the InputSource ID to the default of my input source
        $inputSourceId = HoomdossierSession::getInputSource();

        // And override if necessary
        if ($inputSource instanceof InputSource) {
            $inputSourceId = $inputSource->id;
        } else {
            \Log::debug('ToolSettingTrait: $inputSource is not a instance. this means the input_source_id does not exist on the model and the trait is included in a wrong model !');
        }

        return $inputSourceId;
    }

    public static function bootToolSettingTrait()
    {
        static::created(function (Model $model) {
            $inputSourceId = self::getInputSourceId($model);

            if (! is_null($inputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(),
                    $inputSourceId,
                    true);
            }
        });

        static::updated(function (Model $model) {
            $inputSourceId = self::getInputSourceId($model);

            if (! is_null($inputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(),
                    $inputSourceId,
                    true);
            }
        });
    }
}
