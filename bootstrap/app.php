<?php

use App\Exceptions\RoleInSessionHasNoAssociationWithUser;
use App\Helpers\Hoomdossier;
use App\Helpers\RoleHelper;
use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use App\Models\Role;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Sentry\Laravel\Integration;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        //api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::prefix('api')
                ->as('api.')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            $params = ['cooperation' => $request->route('cooperation')];

            if ($request->route()?->getName() === 'cooperation.auth.verification.verify') {
                // So a user is trying to verify his account but isn't logged in. We will pass the account ID onward
                // so the form can autofill the email for this user.
                $params['id'] = $request->route('id');

                // We will warn them requiring to log in first.
                session()->flash('status', __('cooperation/auth/verify.require-auth'));
            }

            route('cooperation.auth.login', $params);
        });
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
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        $exceptions->render(function (RoleInSessionHasNoAssociationWithUser $e, Request $request) {
            // try to obtain a role from the user.
            $role = Hoomdossier::user()->roles()->first();

            $role instanceof Role
                ? HoomdossierSession::setRole($role)
                : Hoomdossier::user()->logout();

            return redirect()->route('cooperation.home');
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if (HoomdossierSession::hasRole()) {
                // the role the user currently has in his session
                $authorizedRole = HoomdossierSession::getRole(true);

                return redirect(
                    url(RoleHelper::getUrlByRoleName($authorizedRole->name))
                )->with('warning', __('default.messages.exceptions.no-right-roles'));
            }

            return redirect()->route('cooperation.home');
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            $cooperation = $request->route('cooperation');

            if (! empty($cooperation)) {
                Log::debug("cooperation is not empty ( = '{$cooperation}')");
                $redirect = CooperationRedirect::from($cooperation)->first();

                if ($redirect instanceof CooperationRedirect) {
                    Log::debug("Redirect to " . str_ireplace(
                            $cooperation,
                            $redirect->cooperation->slug,
                            $request->url()
                        ));
                    return redirect(
                        str_ireplace(
                            $cooperation,
                            $redirect->cooperation->slug,
                            $request->url()
                        )
                    );
                }
            }

            // Fall back to normal exception rendering
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // get them directly from the session itself
            // the HoomdossierSession can only be used on authenticated parts.
            $cooperationId = session()->get('cooperation', null);

            if (is_null($cooperationId)) {
                return redirect()->route('index');
            }

            $cooperation = Cooperation::find($cooperationId);
            if (! $cooperation instanceof Cooperation) {
                return redirect()->route('index');
            }

            return redirect()->guest($e->redirectTo($request) ?? route('cooperation.auth.login', compact('cooperation')));
        });
    })->create();
