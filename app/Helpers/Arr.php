<?php

namespace App\Helpers;

class Arr extends \Illuminate\Support\Arr
{
    /**
     * Check if a whole array is empty.
     */
    public static function isWholeArrayEmpty(array $array): bool
    {
        // Dot it, so we don't need unnecessary loops and a recursive stuff.
        $array = static::dot($array);

        foreach ($array as $key => $value) {
            if (! Str::isConsideredEmptyAnswer($value) && ! empty($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if one or many values are all in an array.
     * TODO: Tests
     * @param $needles
     */
    public static function inArray(array $haystack, $needles): bool
    {
        $needles = (array) $needles;

        foreach ($needles as $needle) {
            if (! in_array($needle, $haystack)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if one or one of many values are in an array.
     * TODO: Tests
     * @param $needles
     */
    public static function inArrayAny(array $haystack, $needles): bool
    {
        $needles = (array) $needles;

        foreach ($needles as $needle) {
            if (in_array($needle, $haystack)) {
                return true;
            }
        }

        return false;
    }
}
