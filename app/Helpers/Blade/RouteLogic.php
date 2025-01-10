<?php

namespace App\Helpers\Blade;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class RouteLogic
{
    public static function inSimpleScan($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.frontend.tool.simple-scan');
    }

    /** @deprecated use InScanTool instead. */
    public static function inQuickScanTool($route): bool
    {
        return self::inSimpleScan($route);
    }

    public static function inMyPlan($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::contains($routeName, 'my-plan.');
    }

    public static function inMyRegulations($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::contains($routeName, 'my-regulations.index');
    }

    public static function inQuestionnaire($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::contains($routeName, 'questionnaires.index');
    }

    public static function inExpertTool($route): bool
    {
        $routeName = static::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.tool') || Str::startsWith($routeName, 'cooperation.frontend.tool.expert-scan');
    }

    private static function getRouteName($route): ?string
    {
        return $route instanceof Route ? $route->getName() : $route;
    }
}
