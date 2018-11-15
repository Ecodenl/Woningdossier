<?php

namespace App\Providers;

use App\Helpers\Str;
use App\Http\ViewComposers\CooperationComposer;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\Interest;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Observers\PrivateMessageObserver;
use App\Models\Translation;
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

//            if ($mainMessage->isConversationRequest()) {
//                $mainMessage->getRespons();
//            }

//            dd($mainMessage);
//
//             if the chat is a conversation request there may not be a receive user.
//             so we allow it
//            if (!$receiveUser instanceof User) {
//                dd($receiveUser);
//                return true;
//                if ($receiveUser->hasRole('coordinator')) {
//                    return true;
//                }
//            }

            // if the sender and receiver both have the role coordinator or coach,
            // there is no need to check for the status since they are always allowed to contact eachother
            if ($sendUser->hasRole(['coordinator', 'coach'])  && $receiveUser->hasRole(['coach', 'coordinator'])) {
                return true;
            } else {
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

        \View::creator('*', CooperationComposer::class);

        /**
         * Well here it is.
         *
         * Get a translation from the translations table through the uuid translatable file
         * If the given key exist in the uuid translatable file it wil try to locate a record in the translation table and return that.
         * If it does not exist, we get the given key returned.
         */
//        \Blade::directive('uuidlang', function ($key) {
//
//
//            $translationString = explode(',', $key, 2);
//
//            $replaceArray = [];
//
//            // second "parameter" will be the array that contains the replacements for the translation.
//            if (array_key_exists(1, $translationString)) {
//                // convert the "array string" to a real array
//                $replace = $translationString[1];
//                $replace = str_replace('', '', $replace);
//                $replace = str_replace('[', '', $replace);
//                $replace = str_replace(']', '', $replace);
//                $replace = str_replace("'", '', $replace);
//                $replace = explode(', ', $replace);
//
//                foreach ($replace as $r) {
//                    $keyAndValue = explode('=>', $r);
//                    $replaceArray[trim($keyAndValue[0])] = trim($keyAndValue[1]);
//
//                }
//            }
//
//
//            // Key to the uuid.php translatable file.
//            $translationFileKey = "uuid.".str_replace("'", '', $translationString[0]);
//
//            // Get the uuid from the translation file key
//            $translationUuidKey = __($translationFileKey);
//
//            // if it is a valid uuid get the translation else we will return the translation key.
//            if (Str::isValidUuid($translationUuidKey)) {
//                $translation = Translation::getTranslationFromKey($translationUuidKey);
//
//                if (empty($replaceArray)) {
//                    return $translation;
//                }
//
//                foreach ($replaceArray as $key => $value) {
//                    $translation = str_replace(
//                        [
//                            ':'.$key,
//                        ],
//                        [
//                            $value,
//                        ],
//                        $translation);
//
//                }
//
//                return $translation;
//            } else {
//                return $translationUuidKey;
//            }/**/
//        });
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
