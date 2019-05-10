<?php

namespace App\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AvailableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Global scope so it will only show file's that are available
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('available_until', '>=', Carbon::now()->format('Y-m-d H:i:s'));
    }
}
