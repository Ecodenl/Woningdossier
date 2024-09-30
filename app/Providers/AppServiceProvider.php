<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Rules\MaxFilenameLength;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));

        if ($this->app->environment('local', 'testing')) {
            //$this->app->register(DuskServiceProvider::class);
        }
        if ($this->app->environment('local')) {
            //$this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
