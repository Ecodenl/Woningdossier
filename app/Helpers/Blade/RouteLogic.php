<?php

namespace App\Helpers\Blade;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class RouteLogic
{
    public static function inQuickScanTool($route): bool
    {
        $routeName = $route instanceof Route ? $route->getName() : $route;

        return Str::startsWith($routeName, 'cooperation.quick-scan');
    }

    public static function inExpertTool($route): bool
    {
        $routeName = $route instanceof Route ? $route->getName() : $route;

        return Str::startsWith($routeName, 'cooperation.tool');
    }
}