<?php

namespace App\Providers;

use App\Listeners\EconobisEventSubscriber;
use App\Listeners\QueueEventSubscriber;
use App\Listeners\UserEventSubscriber;
use App\Models\Cooperation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\BuildingPolicy;
use App\Models\PersonalAccessToken;
use App\Rules\MaxFilenameLength;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ----- Validator ----- //
        // After L7 it is no longer in the docs, albeit still present
        // https://laravel.com/docs/7.x/validation#using-extensions

        Validator::extend('needs_to_be_lower_or_same_as', function ($attribute, $value, $parameters, $validator) {
            $formData = Arr::dot($validator->getData());
            $compareFieldValue = $formData[$parameters[0]];

            if ($value > $compareFieldValue) {
                return false;
            } else {
                return true;
            }
        });

        Validator::replacer('needs_to_be_lower_or_same_as', function ($message, $attribute, $rule, $parameters) {
            $compareFieldName = $parameters[0];

            return __('validation.custom')[$attribute][$rule] ?? __('validation.custom.needs_to_be_lower_or_same_as', [
                    'attribute' => __('validation.attributes')[$compareFieldName],
                ]);
        });

        Validator::extend('max_filename_length', function ($attribute, $value, $parameters, $validator) {
            return (new MaxFilenameLength(...$parameters))->passes($attribute, $value);
        });

        Validator::replacer('max_filename_length', function ($message, $attribute, $rule, $parameters) {
            return (new MaxFilenameLength(...$parameters))->message();
        });

        // ----- Routing ----- //
        Route::model('cooperation', Cooperation::class);

        Route::bind('cooperation', function (string $value, \Illuminate\Routing\Route $route) {
            // Let's use a magic method instead of using either $this or $route because Laravel is such a great
            // framework these days that just giving access to data already present is just too much to handle.
            // #FacadesAreASignOfGoodCode #WhatColorIsYourLamborghini
            if (request()->hasHeader('X-Cooperation-Slug')) {
                return Cooperation::whereSlug(request()->header('X-Cooperation-Slug'))->firstOrFail();
            }

            return Cooperation::whereSlug($value)->firstOrFail();
        });

        // ----- Rate limiting ----- //
        // Because L11 doesn't define this for you by default :)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Paginator::useBootstrapThree();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        /**
         * @see  https://spatie.be/docs/laravel-translatable/v6/basic-usage/handling-missing-translations
         */
        Translatable::fallback(App::getFallbackLocale());

        $this->bootAuth();
        $this->attachSubscribers();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));
    }

    public function bootAuth(): void
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        });

        Gate::define('talk-to-resident', BuildingPolicy::class . '@talkToResident');
        Gate::define('access-building', BuildingPolicy::class . '@accessBuilding');
        Gate::define('set-appointment', BuildingPolicy::class . '@setAppointment');
        Gate::define('set-status', BuildingPolicy::class . '@setStatus');

        Gate::define('delete-own-account', UserPolicy::class . '@deleteOwnAccount');
        Gate::define('assign-role', UserPolicy::class . '@assignRole');
        Gate::define('access-admin', UserPolicy::class . '@accessAdmin');
        Gate::define('delete-user', UserPolicy::class . '@deleteUser');
        Gate::define('remove-participant-from-chat', UserPolicy::class . '@removeParticipantFromChat');

        Gate::define('send-user-information-to-econobis', [UserPolicy::class, 'sendUserInformationToEconobis']);
        Gate::define('editAny', [RolePolicy::class, 'editAny']);
    }

    public function attachSubscribers(): void
    {
        Event::subscribe(UserEventSubscriber::class);
        Event::subscribe(QueueEventSubscriber::class);
        Event::subscribe(EconobisEventSubscriber::class);
    }
}
