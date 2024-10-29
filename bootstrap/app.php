<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('cooperation.auth.login',$params));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->validateCsrfTokens(except: [
            'tool/wall-insulation/calculate',
            'tool/insulated-glazing/calculate',
            'tool/floor-insulation/calculate',
            'tool/roof-insulation/calculate',
            'tool/high-efficiency-boiler/calculate',
            'tool/solar-panels/calculate',
            'tool/heater/calculate',
        ]);

        $middleware->web([
            \App\Http\Middleware\CheckForCooperationRedirect::class,
            \App\Http\Middleware\UserLanguage::class,
            \App\Http\Middleware\SentryContext::class,
        ]);

        $middleware->statefulApi();
        $middleware->throttleApi();

        $middleware->replace(\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class, \App\Http\Middleware\PreventRequestsDuringMaintenance::class);

        $middleware->alias([
            'access.cooperation' => \App\Http\Middleware\Api\AllowIfTokenCanAccessCooperation::class,
            'checks-conditions-for-sub-steps' => \App\Http\Middleware\ChecksConditionsForSubSteps::class,
            'cooperation' => \App\Http\Middleware\CooperationMiddleware::class,
            'cooperation-has-scan' => \App\Http\Middleware\CooperationHasScan::class,
            'current-role' => \App\Http\Middleware\CurrentRoleMiddleware::class,
            'deny-if-filling-for-other-building' => \App\Http\Middleware\RedirectIfIsFillingForOtherBuilding::class,
            'deny-if-observing-building' => \App\Http\Middleware\RedirectIfIsObservingBuilding::class,
            'duplicate-data-for-user' => \App\Http\Middleware\DuplicateDataForBuilding::class,
            'ensure-quick-scan-completed' => \App\Http\Middleware\EnsureQuickScanCompleted::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'restore-building-session-if-filling-for-other-building' => \App\Http\Middleware\RestoreBuildingSessionIfFillingForOtherBuilding::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'track-visited-url' => \App\Http\Middleware\TrackVisitedUrl::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
