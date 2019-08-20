<?php

namespace App\Traits;

use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Builder;

trait HasCooperationTrait {

    /**
     * Boot the trait
     */
    public static function bootHasCooperationTrait()
    {
        // only add the scope if the app is not running in the console.
        if (!\App::runningInConsole()) {
            static::addGlobalScope(new CooperationScope());
        }
    }

    public function scopeForMyCooperation(Builder $builder, $cooperationId)
    {
        return $builder->where('cooperation_id', $cooperationId);
    }
}