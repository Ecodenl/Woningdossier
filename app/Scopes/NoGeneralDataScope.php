<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NoGeneralDataScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (\Schema::hasColumn('steps', 'short')) {
            $builder->whereNotIn('short', [
                'general-data',
                'building-characteristics',
                'current-state',
                'usage',
                'interest',
            ]);
        }
    }
}
