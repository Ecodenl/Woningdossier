<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
