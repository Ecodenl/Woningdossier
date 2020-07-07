<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Collection;

class Translation extends BaseCache
{

    const CACHE_KEY_GET_TRANSLATION = 'Translation_getTranslation_%s';

    public static function getTranslationInLanguage($key, $language)
    {

        /** @var Collection $translations */
        $translations = static::getTranslations($key);

        if ($translations->isEmpty()){
            return null;
        }
        return $translations->where('language', '=', $language)->first();

        /*
        $translation = \App\Models\Translation::where('key', $key)
            //->where('language', $language)
                                              ->first();

        dump($translation);

        return $translation;*/
    }

    public static function getTranslations($key)
    {
        return \Cache::rememberForever(
            self::getCacheKey(static::CACHE_KEY_GET_TRANSLATION, $key),
            function () use ($key) {
                return \App\Models\Translation::where('key', $key)
                                              ->get();
            }
        );
    }


    public static function wipe(\App\Models\Translation $translation)
    {
        \Cache::forget(self::getCacheKey(static::CACHE_KEY_GET_TRANSLATION,
            $translation->key));
    }
}