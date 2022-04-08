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
     * Get a translation using the default function.
     *
     * @return array|mixed|string|null
     */
    public static function translate(string $translationString, array $replaceArray = [])
    {
        return __($translationString, $replaceArray);
    }

    /**
     * Returns whether or not there is a (non-empty) translation.
     *
     * @return bool
     */
    public static function hasTranslation(string $translationString, array $replaceArray = [])
    {
        return $translationString !== static::translate($translationString, $replaceArray);
    }
}
