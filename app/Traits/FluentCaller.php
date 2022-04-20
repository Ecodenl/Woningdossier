<?php

namespace App\Traits;

trait FluentCaller {

    public static function init()
    {
        return new static(...func_get_args());
    }
}