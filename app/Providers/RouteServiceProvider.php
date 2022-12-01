<?php

namespace App\Providers;

use App\Models\SubStep;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\Cooperation;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->mapApiRoutes();

            $this->mapWebRoutes();
        });
        Route::model('cooperation', Cooperation::class);

        Route::bind('cooperation', function ($value) {
            if ($this->getCurrentRequest()->hasHeader('X-Cooperation-Slug')) {
                return Cooperation::whereSlug($this->getCurrentRequest()->header('X-Cooperation-Slug'))->firstOrFail();
            }

            return Cooperation::whereSlug($value)->firstOrFail();
        });

        Route::bind('subStep', function ($value) {
            $subStep = (new SubStep());

            dd(
                (new SubStep())->resolveRouteBinding($value, $subStep->getRouteKeyName()),
            );
            dd($subStep->resolveRouteBinding());
//            (new SubStep()->resolveChildRouteBinding('step', 'bier', 'step_id'))
//            return (new SubStep()->resolveChildRouteBinding('step', 'bier', 'step_id'))
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
            ->as('api.')
             ->group(base_path('routes/api.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
