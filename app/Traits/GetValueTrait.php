<?php

namespace App\Traits;

use App\Scopes\GetValueScope;

trait GetValueTrait
{
    /**
     * Boot the scope.
     */
    public static function bootGetValueTrait(): void
    {
        static::addGlobalScope(new GetValueScope());
    }
}
