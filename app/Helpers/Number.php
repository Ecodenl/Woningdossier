<?php

namespace App\Helpers;

use \Illuminate\Support\Str;

class Number
{

    /**
     * Check if a number is negative
     *
     * @param $number
     * @return bool
     */
    public static function isNegative($number): bool
    {
        // while -0.0 isn't officially negative in php standards
        // we define it as negative.
        if (Str::contains($number, '-')) {
            return true;
        }

        return $number < 0;
    }
}