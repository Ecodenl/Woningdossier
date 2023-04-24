<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Rules\MaxFilenameLength;
use App\Services\Models\NotificationService;
use App\Services\Verbeterjehuis\Client as VerbeterJeHuisClient;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use App\Traits\Queue\HasNotifications;
use Carbon\Carbon;
use Ecodenl\LvbagPhpWrapper\Client as LvbagClient;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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
    public function boot()
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

        Queue::before(function (JobProcessing $event) {
            $payload = $event->job->payload();
            $command = unserialize($payload['data']['command']);
            $commandTraits = class_uses_recursive($command);
            $jobName = get_class($command);
            if (in_array(HasNotifications::class, $commandTraits)) {
                $building = $command->building ?? $command->user->building;
                Log::debug("JOB {$jobName} started | b_id: {$building->id} | input_source_id: {$command->inputSource->id}");
            }
        });

        Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            $command = unserialize($payload['data']['command']);
            $commandTraits = class_uses_recursive($command);
            $jobName = get_class($command);
            if (in_array(HasNotifications::class, $commandTraits)) {
                $building = $command->building ?? $command->user->building;
                Log::debug("JOB {$jobName} ended | b_id: {$building->id} | input_source_id: {$command->inputSource->id}");

                $service = NotificationService::init()
                    ->forBuilding($building)
                    ->setType($jobName)
                    ->setUuid($command->uuid);

                // The command might not care about the input source, and so in that case we don't want to query on it.
                if ($command->caresForInputSource) {
                    $service->forInputSource($command->inputSource);
                }

                $service->deactivate();
            }
        });

        Paginator::useBootstrapThree();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->app->bind(LvbagClient::class, function (Application $app) {
            $useProductionEndpoint = true;
            // During testing, we should be mocking, but just in case someone forgets to mock...
            if ($app->isLocal() || $app->environment('testing')) {
                $useProductionEndpoint = false;
            }
            return new LvbagClient(
                config('hoomdossier.services.bag.secret'),
                'epsg:28992',
                $useProductionEndpoint,
            );
        });

        $this->app->bind(Lvbag::class, function (Application $app) {
            return new Lvbag($app->make(LvbagClient::class));
        });

        $this->app->bind(VerbeterJeHuisClient::class, function (Application $app) {
            return new VerbeterJeHuisClient();
        });

        $this->app->bind(Verbeterjehuis::class, function (Application $app) {
            return new Verbeterjehuis($app->make(VerbeterJeHuisClient::class));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
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
