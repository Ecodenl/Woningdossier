<?php

namespace App\Helpers;

class Arr
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
            array_set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Check if a whole array is empty.
     *
     * @param array $array
     * @return bool
     */
    public static function isWholeArrayEmpty(array $array): bool
    {
        foreach($array as $key => $value) {
            if (!Str::isConsideredEmptyAnswer($value)) {
                return false;
            }
        }
        return true;
    }

}
