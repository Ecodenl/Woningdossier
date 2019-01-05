<?php

namespace App\Traits;

use App\Scopes\GetValueScope;

trait GetValueTrait
{
    /**
     * Boot the scope.
     *
     * @return void
     */
    public static function bootGetValueTrait()
    {
        static::addGlobalScope(new GetValueScope());
    }
}
