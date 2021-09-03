<?php

namespace App\Macros\Collection;

use Illuminate\Support\Collection;

/**
 * Replaces the item with a particular value (by key) from the JSON data
 * currently populating the field.
 * Particularly useful when you have JSON (e.g. translations) in the particular
 * field, but you only want the translated label.
 * This happens on several (overview) pages where the query builder is used
 * instead of a model.
 *
 * @param string $field Field containing JSON
 * @param string $renameKey If filled, the translation will be put in this field, if not the same field will be used (replaced)
 * @param string $locale Locale for the translation. If empty, the app's current locale will be used
 * @param $fallback
 *
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return mixed
 */
class PullTranslationFromJson
{
    public function __invoke()
    {
        return function(string $field, string $renameKey = '', string $locale = '', $fallback = null) : Collection {
            if (empty($locale)){
                $locale = app()->getLocale();
            }
            return $this->each(function($item) use ($renameKey, $field, $locale, $fallback) {
                $property = $field;
                if (!empty($renameKey)){
                    $property = $renameKey;
                }
                $item->$property = json_decode($item->$field)->$locale ?? $fallback;
                if (!empty($renameKey)){
                    unset($item->$field);
                }
            });
        };
    }
}