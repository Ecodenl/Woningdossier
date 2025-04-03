<?php

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

EloquentBuilder::macro('withWhereHas', function ($relation, Closure $callback = null, $operator = '>=', $count = 1) {
    /**
     * @var EloquentBuilder $this
     *
     * @see https://github.com/laravel/framework/blob/1c2eef82924ff5f9ae14b0fad9f70f5f922a0d17/src/Illuminate/Database/Eloquent/Concerns/QueriesRelationships.php#L166
     */
    return $this->whereHas(Str::before($relation, ':'), $callback, $operator, $count)
        ->with($callback ? [$relation => fn ($query) => $callback($query)] : $relation);
});
