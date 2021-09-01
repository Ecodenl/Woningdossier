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
        $array = \Illuminate\Support\Arr::dot($array);

        foreach ($array as $key => $value) {
            if (! Str::isConsideredEmptyAnswer($value)) {
                return false;
            }
        }

        return true;
    }
}
