<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class NoGeneralDataScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Schema::hasColumn('steps', 'short')) {
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
