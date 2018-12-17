<?php

namespace App\Providers;

use App\Http\ViewComposers\CooperationComposer;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\Interest;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Observers\PrivateMessageObserver;
use App\Models\UserActionPlanAdvice;
use App\Observers\UserActionPlanAdviceObserver;
use App\Models\Step;
use Illuminate\Support\ServiceProvider;

class WoningdossierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        PrivateMessage::observe(PrivateMessageObserver::class);

        \Gate::define('respond', function ($user, $mainMessageId) {


            $mainMessage = PrivateMessage::find($mainMessageId);
            $receiveUser = User::find($mainMessage->to_user_id);
            $sendUser = User::find($mainMessage->from_user_id);


            // if the sender and receiver both have the role coordinator or coach,
            // there is no need to check for the status since they are always allowed to contact eachother
            if ($sendUser->hasRole(['coordinator', 'coach'])  && $receiveUser->hasRole(['coach', 'coordinator'])) {
                return true;
            } else {
                // if the to user id is empty, its probbaly a message thats send to the cooperation
                if (empty($mainMessage->to_user_id)) {
                    return true;
                }
                // this is NOT the request to the cooperation.
                // this is the mainMessage from the current chat with resident and coach
                $building = Building::where('user_id', $mainMessage->to_user_id)->first();


                // either the coach or the coordinator, or someone with a higher role then resident.
                $fromId = $mainMessage->from_user_id;
                // get the most recent building coach status
                $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $fromId)->where('building_id', $building->id)->get()->last();

                if ($buildingCoachStatus->status == BuildingCoachStatus::STATUS_REMOVED) {
                    return false;
                }

                return true;
            }

        });

        /**
         * Check if a coach can create a appointment
         */
        \Gate::define('make-appointment', function ($user, $buildingId) {
            $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $user->id)->where('building_id', $buildingId)->get()->last();

            if ($buildingCoachStatus->status == BuildingCoachStatus::STATUS_REMOVED) {
                return false;
            }

            return true;
        });

        \Gate::define('access-building', function ($user, $buildingId) {
            $buildingCoachStatus = BuildingCoachStatus::where('building_id', $buildingId)->where('coach_id', $user->id)->get()->last();
            $conversationRequest = PrivateMessage::find($buildingCoachStatus->private_message_id);

            if ($user->hasBuildingPermission($buildingId) && $conversationRequest->allow_access) {
                return true;
            }
            return false;
        });

        \View::composer('cooperation.tool.includes.interested', function ($view) {
            $view->with('interests', Interest::orderBy('order')->get());
        });

        \View::composer('*', function ($view) {
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
        });

        \View::composer('cooperation.tool.*', function ($view) {
            $slug = str_replace('/tool/', '', request()->getRequestUri());
            $step = Step::where('slug', $slug)->first();

            $view->with('currentStep', $step);
        });

        \View::creator('*', CooperationComposer::class);

        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
            }

            return $cooperation;
        });

        $this->app->bind('CooperationStyle', function () {
            $cooperationStyle = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
                if ($cooperation instanceof Cooperation) {
                    $cooperationStyle = $cooperation->style;
                }
            }

            return $cooperationStyle;
        });
    }
}
