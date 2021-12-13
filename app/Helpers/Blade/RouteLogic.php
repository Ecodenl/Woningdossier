<?php

namespace App\Helpers\Blade;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class RouteLogic
{
    public static function inQuickScanTool($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.frontend.tool.quick-scan');
    }

    public static function inMyPlan($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::contains($routeName, 'my-plan.index');
    }

    public static function inQuestionnaire($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::contains($routeName, 'questionnaires.index');
    }

    public static function inExpertTool($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.tool');
    }

    private static function getRouteName($route): ?string
    {
        return $route instanceof Route ? $route->getName() : $route;
    }
}