<?php

namespace App\Traits;

use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait HasCooperationTrait
{
    /**
     * Boot the trait.
     */
    public static function bootHasCooperationTrait()
    {
        // only add the scope if the app is not running in the console.
        if (! App::runningInConsole()) {
//        if (app()->environment() == 'accept' || app()->environment() == 'master') {
            static::addGlobalScope(new CooperationScope());
        }
    }

    public function scopeForMyCooperation(Builder $builder, $cooperationId)
    {
        return $builder
            ->withoutGlobalScope(CooperationScope::class)
            ->where('cooperation_id', $cooperationId);
    }

    public function scopeForAllCooperations(Builder $query)
    {
        return $query->withoutGlobalScope(new CooperationScope());
    }
}
