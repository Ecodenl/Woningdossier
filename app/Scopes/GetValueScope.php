<?php

namespace App\Scopes;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
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

        $builder->where('input_source_id', $inputSourceValueId);

    }
}
