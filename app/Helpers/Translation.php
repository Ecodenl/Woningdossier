<?php

namespace App\Helpers;

class Translation
{
    /**
     * Get a translation using the default function.
     */
    public static function translate(string $translationString, array $replaceArray = []): string
    {
        return __($translationString, $replaceArray);
    }

    /**
     * Returns whether or not there is a (non-empty) translation.
     */
    public static function hasTranslation(string $translationString, array $replaceArray = []): bool
    {
        return $translationString !== static::translate($translationString, $replaceArray);
    }

    /**
     * Translate an attribute literally
     */
    public static function translateAttribute(string $attribute): string
    {
        $attributes = __('validation.attributes');

        if (isset($attributes[$attribute])) {
            return $attributes[$attribute];
        } else {
            // Check potential wild cards
            $pattern = '\.\d\.';
            $attribute = preg_replace("/{$pattern}/i", '.*.', $attribute);

            return $attributes[$attribute] ?? $attribute;
        }
    }
}
