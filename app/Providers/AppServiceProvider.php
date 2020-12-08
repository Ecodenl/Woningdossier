<?php

namespace App\Providers;

use App\Jobs\RecalculateStepForUser;
use App\Listeners\RecalculateToolForUserListener;
use App\Models\Log;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
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
            $formData = array_dot($validator->getData());
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

            if(get_class($command) == RecalculateStepForUser::class) {
                Notification::forMe($command->user)->updateOrCreate([
                    'input_source_id' => $command->inputSource->id,
                    'type' => 'recalculate',
                    // the building owner is always passed to the job.
                    'building_id' => $command->user->building->id
                ], ['is_active' => true]);
            }
        });

        \Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            /** @var RecalculateStepForUser $command */
            $command = unserialize($payload['data']['command']);

            if(get_class($command) == RecalculateStepForUser::class) {
                Notification::forMe($command->user)->updateOrCreate([
                    'input_source_id' => $command->inputSource->id,
                    'type' => 'recalculate',
                    // the building owner is always passed to the job.
                    'building_id' => $command->user->building->id
                ], ['is_active' => false]);
            }
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

        if ($this->app->environment('local', 'testing')) {
            //$this->app->register(DuskServiceProvider::class);
        }
        if ($this->app->environment('local')) {
            //$this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
