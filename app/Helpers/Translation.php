<?php

namespace App\Helpers;

class Translation  {

    /**
     * Get the translation from the translations table based on uuid
     *
     * @param $uuid
     * @return mixed|string
     */
    protected static function getTranslationFromUuid($uuid)
    {
        return \App\Models\Translation::getTranslationFromKey($uuid);
    }

    /**
     *
     * Well here it is.
     *
     * Get a translation from the translations table through the uuid translatable file
     * If the given key exist in the uuid translatable file it wil try to locate a record in the translation table and return that.
     * If it does not exist, we get the given key returned.
     *
     * @param string $translationString
     * @param array $replaceArray
     * @return array|mixed|null|string
     */
    public static function translate(string $translationString, array $replaceArray = []): string
    {

        // Key to the uuid.php translatable file.
        $translationFileKey = "uuid.".str_replace("'", '', $translationString);

        // Get the uuid from the translation file key
        $translationUuidKey = __($translationFileKey);

        // if it is a valid uuid get the translation else we will return the translation key.
        if (Str::isValidUuid($translationUuidKey)) {
            $translation = self::getTranslationFromUuid($translationUuidKey);

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
            return $translationFileKey;
        }
    }
}