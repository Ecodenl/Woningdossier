<?php

namespace App\Helpers;

use App\Models\Translation;
use Illuminate\Support\Collection;

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
     * Delete translations.
     *
     * @param string $key model attribute name
     *
     * @throws \Exception
     */
    public function deleteTranslations(string $key)
    {
        $translationUuid = parent::getAttribute($key);
        Translation::where('key', $translationUuid)->delete();
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
        return Str::isValidUuid($uuid);
    }

    /**
     * @param string      $attribute Model attribute
     * @param string|null $lang      Locale to translate to
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
     * Model classes will use this trait. They implement the getTable method.
     *
     * @return string
     */
    abstract public function getTable();

    /**
     * Scope a query to check translations table.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $attribute Table column
     * @param string                                $name      Translation text
     * @param string                                $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeTranslated($query, $attribute, $name, $locale = 'nl')
    {
        return $query->where('translations.language', '=', $locale)
                    ->where('translations.translation', '=', $name)
                    ->join('translations', $this->getTable().'.'.$attribute, '=', 'translations.key');
    }

    /**
     * Return all the translations that are available in a collection.
     *
     * @param string $attribute default 'name' since this is the most common used field
     *
     * @return Collection
     */
    public function getAllTranslations(string $attribute = 'name'): Collection
    {
        // we use parent::getAttribute or it would return the translated text
        return Translation::where('key', parent::getAttribute($attribute))->get();
    }
}
