<?php

namespace App\Helpers\Views;

use Illuminate\Support\Arr;

/**
 * The "old" values of old will be a bit off for the example buildings, the default old value retrieval wont work
 * this class will deal with it
 * Class ExampleBuildingOldHelper
 * @package App\Helpers\Views
 */
class ExampleBuildingOldHelper {

    public static function old($key, $default)
    {
        return Arr::dot(old())[$key] ?? $default;
    }
}