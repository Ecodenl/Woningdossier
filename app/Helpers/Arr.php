<?php

namespace App\Helpers;

class Arr extends \Illuminate\Support\Arr
{
    /**
     * The inverse of array_dot.
     *
     * @param array $content array of dotted keys to values
     *
     * @return array
     */
    public static function arrayUndot($content)
    {
        $array = [];
        foreach ($content as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return $array;
    }

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
     * @param  array  $haystack
     * @param $needles
     *
     * @return bool
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
     * @param  array  $haystack
     * @param $needles
     *
     * @return bool
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
