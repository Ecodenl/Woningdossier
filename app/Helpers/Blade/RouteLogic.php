<?php

namespace App\Helpers\Blade;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class RouteLogic
{
    public static function inSimpleScan($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.frontend.tool.simple-scan');
    }

    /** @deprecated use InScanTool instead. */
    public static function inQuickScanTool($route): bool
    {
        return self::inSimpleScan($route);
    }

    public static function inMyPlan($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::contains($routeName, 'my-plan.');
    }

    public static function inMyRegulations($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::contains($routeName, 'my-regulations.index');
    }

    /*
     * The four below carve the frontend up the way the SmartTwin navigation presents it. They are
     * sections of the site rather than steps of the scan, which is why they don't line up one to one
     * with the route groups: the file overview lives inside the my-plan routes but is its own
     * navigation item, so inWoningdossier() has to hand it over.
     */

    public static function inDashboard($route): bool
    {
        return self::getRouteName($route) === 'cooperation.home';
    }

    public static function inWoningdossier($route): bool
    {
        return self::inSimpleScan($route) && ! self::inSharedFiles($route);
    }

    public static function inSharedFiles($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::contains($routeName, 'my-plan.media');
    }

    public static function inMessages($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::contains($routeName, ['.messages.', 'conversation-requests.']);
    }

    public static function inMyAccount($route): bool
    {
        $routeName = self::getRouteName($route);

        // The message routes sit under my-account but have a navigation item of their own.
        return Str::startsWith($routeName, 'cooperation.my-account') && ! self::inMessages($routeName);
    }

    public static function inQuestionnaire($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::contains($routeName, 'questionnaires.index');
    }

    public static function inExpertTool($route): bool
    {
        $routeName = self::getRouteName($route);

        return Str::startsWith($routeName, 'cooperation.tool') || Str::startsWith($routeName, 'cooperation.frontend.tool.expert-scan');
    }

    private static function getRouteName($route): ?string
    {
        return $route instanceof Route ? $route->getName() : $route;
    }
}
