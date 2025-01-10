<?php

namespace App\Traits;

trait FluentCaller
{
    public static function init(): self
    {
        return new self(...func_get_args());
    }
}
