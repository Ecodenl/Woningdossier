<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;

trait GetValueTrait {

    /**
     * Boot the scope.
     *
     * @return void
     */
    public static function bootGetValueTrait()
    {
        static::addGlobalScope(new GetValueScope);
    }

    /**
     * Get the correct value / row from a building_ table
     *
     * @return getValue|null
     */
    /**
    public function getValues()
    {

        $inputSourceValueId = HoomdossierSession::getInputSourceValue();
        $inputSourceId = HoomdossierSession::getInputSource();

        // On login, the user input source id and input source value id will be set to the same input source id.
        // The input source value id will be changed when a user changes the input source id by himself
        // so if the input source id != input source value id the user changed it and we can just do a where
        if ($inputSourceId != $inputSourceValueId) {
            $buildingValue = $this->where('input_source_id', $inputSourceId);
        } else {
            // Else we will get the best input source.
            // get the input sources from the current
            $buildingValues = $this->leftJoin('input_sources', 'input_sources.id', '=', $this->getTable().'.input_source_id')
                ->orderBy('order', 'desc')
                ->where($this->getTable().'.building_id', '=', HoomdossierSession::getBuilding())
                ->select('input_sources.id as input_source_id', 'input_sources.name as input_source_name', 'input_sources.short as input_source_short', 'input_sources.order as input_source_order', $this->getTable().'.*')
                ->get();

            $buildingValue = null;

            if(!$buildingValues->isEmpty()) {

                // Check if the set input source has records (The currently logged in user)
                if ($buildingValues->contains('input_source_id', $inputSourceId)) {
                    $buildingValue = $buildingValues->where('input_source_id', $inputSourceId)->first();

                } else {
                    // else get the most reliable input source
                    $buildingValue = $buildingValues->first();
                }
            }
        }

        return $buildingValue;

    }
    */
}