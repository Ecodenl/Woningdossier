<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\BuildingPolicy;
use App\Models\PersonalAccessToken;
use App\Rules\MaxFilenameLength;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
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

        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            return $this->where($attribute, 'LIKE', "%{$searchTerm}%");
        });

        Collection::macro('addArrayOfWheres', function ($array, $method, $boolean) {
            $this->whereNested(function ($query) use ($array, $method, $boolean) {
                foreach ($array as $key => $value) {
                    if (is_numeric($key) && is_array($value)) {
                        $query->{$method}(...array_values($value));
                    } else {
                        $query->$method($key, '=', $value, $boolean);
                    }
                }
            }, $boolean);
        });

        Paginator::useBootstrapThree();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        /**
         * @see  https://spatie.be/docs/laravel-translatable/v6/basic-usage/handling-missing-translations
         */
        Translatable::fallback(App::getFallbackLocale());

        $this->bootAuth();
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
            return 'App\\Policies\\'.class_basename($modelClass).'Policy';
        });

        Gate::define('talk-to-resident', BuildingPolicy::class.'@talkToResident');
        Gate::define('access-building', BuildingPolicy::class.'@accessBuilding');
        Gate::define('set-appointment', BuildingPolicy::class.'@setAppointment');
        Gate::define('set-status', BuildingPolicy::class.'@setStatus');

        Gate::define('delete-own-account', UserPolicy::class.'@deleteOwnAccount');
        Gate::define('assign-role', UserPolicy::class.'@assignRole');
        Gate::define('access-admin', UserPolicy::class.'@accessAdmin');
        Gate::define('delete-user', UserPolicy::class.'@deleteUser');
        Gate::define('remove-participant-from-chat', UserPolicy::class.'@removeParticipantFromChat');

        Gate::define('send-user-information-to-econobis', [UserPolicy::class, 'sendUserInformationToEconobis']);
        Gate::define('editAny', [RolePolicy::class, 'editAny']);
    }
}
