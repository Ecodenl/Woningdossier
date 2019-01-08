<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Database\Eloquent\Model;

trait ToolSettingTrait {

    /**
     * Returns a inputsourceid
     *
     * @param Model $model
     * @return int|mixed
     */
    public static function getInputSourceId(Model $model)
    {
        $inputSource = InputSource::find($model->input_source_id);

        // the inputsource session is always set for logged in users, so we get it from there so we always have a input source
        // however, if a example building gets saved this is not the kees. So if we can get it from the saved model, we do so.
        $inputSourceId = HoomdossierSession::getInputSource();

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

            ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $inputSourceId, true);
        });

        static::updated(function (Model $model) {
            $inputSourceId = self::getInputSourceId($model);

            ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $inputSourceId, true);
        });
    }
}