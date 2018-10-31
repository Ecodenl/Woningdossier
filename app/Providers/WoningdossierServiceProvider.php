<?php

namespace App\Providers;

use App\Helpers\Str;
use App\Http\ViewComposers\CooperationComposer;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
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
        //view()->composer('cooperation.layouts.app',  CooperationComposer::class);
        //view()->composer('*',  CooperationComposer::class);

        \View::composer('cooperation.tool.includes.interested', function ($view) {
            $view->with('interests', Interest::orderBy('order')->get());
        });

        \View::composer('*', function ($view) {
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
        });

        \View::creator('*', CooperationComposer::class);

        /**
         * Well here it is.
         *
         * Get a translation from the translations table through the uuid translatable file
         * If the given key exist in the uuid translatable file it wil try to locate a record in the translation table and return that.
         * If it does not exist, we get the given key returned.
         */
        \Blade::directive('uuidlang', function ($key) {

            $translationString = explode(',', $key, 2);

            $replaceArray = [];

            // second "parameter" will be the array that contains the replacements for the translation.
            if (array_key_exists(1, $translationString)) {
                // convert the "array string" to a real array
                $replace = $translationString[1];
                $replace = str_replace('', '', $replace);
                $replace = str_replace('[', '', $replace);
                $replace = str_replace(']', '', $replace);
                $replace = str_replace("'", '', $replace);
                $replace = explode(', ', $replace);

                foreach ($replace as $r) {
                    $keyAndValue = explode('=>', $r);
                    $replaceArray[trim($keyAndValue[0])] = trim($keyAndValue[1]);

                }
            }
            // get the uuid key from the uuid translation file
            $translationUuidKey = __("uuid.".str_replace("'", '', $translationString[0]));
            // if it is a valid uuid get the translation else we will return the translation key.
            if (Str::isValidUuid($translationUuidKey)) {
                $translation = Translation::getTranslationFromKey($translationUuidKey);

                if (empty($replaceArray)) {
                    return $translation;
                }

                foreach ($replaceArray as $key => $value) {
                    $translation = str_replace(
                        [
                            ':'.$key,
                        ],
                        [
                            $value,
                        ],
                        $translation);

                }

                return $translation;
            } else {
                return $translationUuidKey;
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //\Log::debug(__METHOD__);

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
