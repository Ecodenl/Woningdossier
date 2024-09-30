<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LanguageRequired implements Rule
{
    protected string $requiredLocale = '';

    protected bool $required = true;

    protected string $attribute = '';

    /**
     * Create a new rule instance.
     *
     * @param string $requiredLocale
     * @param string $required
     */
    public function __construct(string $requiredLocale = 'nl', string $required = true)
    {
        $this->requiredLocale = $requiredLocale;
        $this->required = $required;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        $this->attribute = $attribute;
        $requiredTranslation = $value[$this->requiredLocale] ?? null;

        if (! empty($requiredTranslation) && ! is_numeric($requiredTranslation)) {
            return true;
        }

        // If it doesn't pass we will still pass it if it is not required
        return ! $this->required;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.custom-rules.language-required', [
            'attribute' => __('validation.attributes')[$this->attribute] ?? $this->attribute,
            'locale' => __('validation.attributes')[$this->requiredLocale] ?? $this->requiredLocale,
        ]);
    }
}