<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class LanguageRequired implements ValidationRule
{
    protected string $requiredLocale = '';

    protected bool $required = true;

    protected string $attribute = '';

    /**
     * Create a new rule instance.
     */
    public function __construct(string $requiredLocale = 'nl', bool $required = true)
    {
        $this->requiredLocale = $requiredLocale;
        $this->required = $required;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
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
     */
    public function message(): string
    {
        return __('validation.custom-rules.language-required', [
            'attribute' => __('validation.attributes')[$this->attribute] ?? $this->attribute,
            'locale' => __('validation.attributes')[$this->requiredLocale] ?? $this->requiredLocale,
        ]);
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}