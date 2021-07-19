<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LanguageRequired implements Rule
{
    protected string $requiredLocale = '';

    protected string $attribute = '';

    /**
     * Create a new rule instance.
     *
     * @param string $requiredLocale
     */
    public function __construct($requiredLocale = 'nl')
    {
        $this->requiredLocale = $requiredLocale;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $requiredTranslation = $value[$this->requiredLocale] ?? null;

        if (! is_null($requiredTranslation)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.custom-rules.language-required', [
            'attribute' => __('validation.attributes')[$this->attribute] ?? $this->attribute,
            'locale' => __('validation.attributes')[$this->requiredLocale] ?? $this->requiredLocale,
        ]);
    }
}
