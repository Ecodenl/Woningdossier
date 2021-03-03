<?php

namespace App\Providers;

use App\Jobs\RecalculateStepForUser;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

//use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('needs_to_be_lower_or_same_as', function ($attribute, $value, $parameters, $validator) {
            $formData = Arr::dot($validator->getData());
            $compareFieldValue = $formData[$parameters[0]];

            if ($value > $compareFieldValue) {
                return false;
            } else {
                return true;
            }
        });

        \Validator::replacer('needs_to_be_lower_or_same_as', function ($message, $attribute, $rule, $parameters) {
            $compareFieldName = $parameters[0];

            return __('validation.custom')[$attribute][$rule] ?? __('validation.custom.needs_to_be_lower_or_same_as', [
                    'attribute' => __('validation.attributes')[$compareFieldName],
                ]);
        });

        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            return $this->where($attribute, 'LIKE', "%{$searchTerm}%");
        });

        \Queue::before(function (JobProcessing $event) {
            $payload = $event->job->payload();
            /** @var RecalculateStepForUser $command */
            $command = unserialize($payload['data']['command']);

            if (RecalculateStepForUser::class == get_class($command)) {
                Log::debug("JOB RecalculateStepForUser started | b_id: {$command->user->building->id} | input_source_id: {$command->inputSource->id}");
                Notification::setActive($command->user->building, $command->inputSource, true);
            }
        });

        \Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            /** @var RecalculateStepForUser $command */
            $command = unserialize($payload['data']['command']);

            if (RecalculateStepForUser::class == get_class($command)) {
                Log::debug("JOB RecalculateStepForUser ended | b_id: {$command->user->building->id} | input_source_id: {$command->inputSource->id}");
                Notification::setActive($command->user->building, $command->inputSource, false);
            }
        });

        Paginator::useBootstrapThree();
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
