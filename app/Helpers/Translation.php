<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class Translation
{
    /**
     * Get the translation from the translations table based on uuid.
     *
     * @param $uuid
     *
     * @return mixed|string
     */
    protected static function getTranslationFromUuid($uuid)
    {
        return \App\Models\Translation::getTranslationFromKey($uuid);
    }

    /**
     * Well here it is.
     *
     * Get a translation from the translations table through the uuid translatable file
     * If the given key exist in the uuid translatable file it wil try to locate a record in the translation table and return that.
     * If it does not exist, we get the given key returned.
     *
     * @param string $translationString
     * @param array  $replaceArray
     *
     * @return array|mixed|string|null
     */
    public static function translate(string $translationString, array $replaceArray = [])
    {
        if (Uuid::isValid($translationString)) {
            \Log::debug('Deprecate me: UUID translation is used: '.$translationString.' with replaceArray: '.json_encode($replaceArray));
        }
        // Get the uuid from the translation file key
        $translation = __($translationString, $replaceArray);

        return $translation;
    }

    /**
     * Returns whether or not there is a (non-empty) translation.
     *
     * @param string $translationString
     * @param array  $replaceArray
     *
     * @return bool
     */
    public static function hasTranslation(string $translationString, array $replaceArray = [])
    {
        return true;
//        return $translationString !== static::translate($translationString, $replaceArray);
    }
}
