<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Log;
use App\Services\ToolSettingService;
use Illuminate\Database\Eloquent\Model;

trait ToolSettingTrait
{
    /**
     * @var array of columns to check
     */
    public $toolSettingColumnsToCheck = [];

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

    /**
     * Can be set on the models and return specific columns to check
     * Sometimes we dont want to check all the columns on a model
     *
     * @return array
     */
    public static function toolSettingColumnsToCheck()
    {
        return [];
    }

    /**
     * Check if a model has changed
     *
     * @param  Model  $model
     *
     * @return bool
     */
    private static function hasChanged(Model $model): bool
    {
        $columnsToCheckForChanges = static::toolSettingColumnsToCheck();

        $hasChanged = false;

        // if there are specific columns to check we will check the property for a change.
        if (!empty($columnsToCheckForChanges)) {
            // walk through it.
            foreach ($columnsToCheckForChanges as $column) {
                // check if it is dirty, if so we will set the bool to true,
                if ($model->isDirty($column)) {
                    $hasChanged = true;
                }
            }
            // no specific columns to check are found, so if the model is saved or updated we set the bool to true.
        } else {
            $hasChanged = true;
        }

        return $hasChanged;
    }

    public static function bootToolSettingTrait()
    {
        static::created(function (Model $model) {

            $hasChanged = static::hasChanged($model);

            if ($model instanceof Building) {
                // When the first page (BuildingDetails) page is done, the example_building_id is ALWAYS set.
                // SO: $building->example_building_id always has a value.
                // BUT: We want to check if the example_building_id is set in this action, so we check
                // the original value of example_building_id. If the example_building_id is dirty we
                // get a different example_building_id back. If it's null, we know that this action was
                // performed on the BuildingDetails page and we set $hasChanged to false so no alert
                // will be triggered.
                $previousExampleBuildingId = $model->getOriginal('example_building_id');
                if(is_null($previousExampleBuildingId)){
                    $hasChanged = false;
                }
            }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (! is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });

        static::updated(function (Model $model) {

            $hasChanged = static::hasChanged($model);

            \Log::debug("ToolSettingService / relations for " . get_class($model) . " " . json_encode($model->relationsToArray()));

            if ($model instanceof Building) {
                // When the first page (BuildingDetails) page is done, the example_building_id is ALWAYS set.
                // SO: $building->example_building_id always has a value.
                // BUT: We want to check if the example_building_id is set in this action, so we check
                // the original value of example_building_id. If the example_building_id is dirty we
                // get a different example_building_id back. If it's null, we know that this action was
                // performed on the BuildingDetails page and we set $hasChanged to false so no alert
                // will be triggered.
                $previousExampleBuildingId = $model->getOriginal('example_building_id');
                if(is_null($previousExampleBuildingId)){
                    $hasChanged = false;
                }
            }

            $changedInputSourceId = self::getChangedInputSourceId($model);

            if (! is_null($changedInputSourceId)) {
                ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $changedInputSourceId, $hasChanged);
            }
        });
    }
}
