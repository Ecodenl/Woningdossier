<?php

namespace App\Helpers;

use App\Models\Translation;

trait TranslatableTrait
{
    /**
     * @param $key
     *
     * @return mixed|string
     */
    public function getAttribute($key)
    {
        $attribute = parent::getAttribute($key);

        if ($this->isValidUuid($attribute)) {
            return $this->translate($attribute, app()->getLocale());
        }

        return $attribute;
    }

    /**
     * Check if a given string is a valid UUID.
     *
     * @param string $uuid The string to check
     *
     * @return bool
     */
    protected function isValidUuid($uuid)
    {
        if (! is_string($uuid) || (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid))) {
            return false;
        }

        return true;
    }

    /**
     * @param string      $attribute Model attribute
     * @param null|string $lang      Locale to translate to
     *
     * @return mixed|string
     */
    public function translate($attribute, $lang = null)
    {
        if (is_null($lang)) {
            $lang = app()->getLocale();
        }
        $translation = Translation::where('key', $attribute)
            ->where('language', $lang)
            ->first();

        if (! $translation instanceof Translation) {
            return $this->$attribute;
        }

        return $translation->translation;
    }

    public function getTranslations($key)
    {
        $attribute = parent::getAttribute($key);
        if ($this->isValidUuid($attribute)) {
            return Translation::where('key', $attribute)->get();
        }

        return $attribute;
    }

    /**
     * @param string $key    attribute name
     * @param string $locale
     *
     * @return Translation|mixed
     */
    public function getTranslation($key, $locale)
    {
        $attribute = parent::getAttribute($key);
        if ($this->isValidUuid($attribute)) {
            return Translation::where('key', $attribute)->where('language', $locale)->first();
        }

        return $attribute;
    }

    public function updateTranslation($key, $text, $locale)
    {
        // if $translation is null, the translation is probably there, but only
        // for another language
        $translation = $this->getTranslation($key, $locale);
        if ($translation instanceof Translation) {
            $translation->translation = $text;
            $translation->save();
        } else {
            $attribute = parent::getAttribute($key);
            if ($this->isValidUuid($attribute)) {
                // There is a UUID. We'll create a translation for this UUID +
                // locale combination
                Translation::updateOrCreate([
                    'key' => $attribute,
                    'language' => $locale,
                ], ['translation' => $text]);
            }
        }
    }

    /**
     * @param array $localizedTexts
     *
     * @return string The translation UUID
     */
    public function createTranslations($attribute, array $localizedTexts)
    {
        $key = Str::uuid();
        foreach ($localizedTexts as $language => $translation) {
            if (is_null($translation)) {
                $translation = '';
            }
            Translation::create(compact('key', 'translation', 'language'));
        }
        parent::setAttribute($attribute, $key);

        return $key;
    }

    /**
     * Scope a query to check translations table.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTranslated($query, $attribute, $name, $locale = 'nl')
    {
        return $query->where('translations.language', '=', $locale)
                    ->where('translations.translation', '=', $name)
                    ->join('translations', $this->getTable().'.'.$attribute, '=', 'translations.key');
    }
}
