<?php

namespace App\Http;

use App\Http\Middleware\CheckRedirects;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UserLanguage::class,
            \App\Http\Middleware\SentryContext::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'verified' =>  \App\Http\Middleware\EnsureEmailIsVerified::class,
        'cooperation' => \App\Http\Middleware\CooperationMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'filled-step' => \App\Http\Middleware\FilledStep::class,
//        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'current-role' => \App\Http\Middleware\CurrentRoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'deny-if-filling-for-other-building' => \App\Http\Middleware\RedirectIfIsFillingForOtherBuilding::class,
        'deny-if-observing-building' => \App\Http\Middleware\RedirectIfIsObservingBuilding::class,
        'restore-building-session-if-filling-for-other-building' => \App\Http\Middleware\RestoreBuildingSessionIfFillingForOtherBuilding::class,

        // quick scan
        'checks-conditions-for-sub-steps' => \App\Http\Middleware\ChecksConditionsForSubSteps::class,

        'track-visited-url' => \App\Http\Middleware\TrackVisitedUrl::class,
        // Expert tool
        'ensure-quick-scan-completed' => \App\Http\Middleware\EnsureQuickScanCompleted::class,

        'duplicate-data-for-user' => \App\Http\Middleware\DuplicateDataForBuilding::class,

        // api
        'access.cooperation' => \App\Http\Middleware\Api\AllowIfTokenCanAccessCooperation::class,
    ];
    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
