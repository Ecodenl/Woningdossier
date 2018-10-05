<?php

namespace App\Scopes;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GetValueScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $inputSourceValueId = HoomdossierSession::getInputSourceValue();
        $inputSourceId = HoomdossierSession::getInputSource();

        // On login, the user input source id and input source value id will be set to the same input source id.
        // The input source value id will be changed when a user changes the input source id by himself
        // so if the input source id != input source value id the user changed it and we can just do a where
        if ($inputSourceId != $inputSourceValueId) {
            $builder->where('input_source_id', $inputSourceValueId);
        } else {
            // Else we will get the best input source.
            // get the input sources from the current
            $builder->leftJoin('input_sources', 'input_sources.id', '=', $model->getTable() . '.input_source_id')
                ->orderBy('input_sources.order')
                ->where($model->getTable() . '.building_id', '=', HoomdossierSession::getBuilding())
                ->select('input_sources.id as input_source_id', 'input_sources.name as input_source_name', 'input_sources.short as input_source_short', 'input_sources.order as input_source_order', $model->getTable() . '.*');

        }
    }
}
