<?php

namespace App\Rules;

use App\Helpers\Translation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LanguageRequired implements ValidationRule
{
    protected string $requiredLocale = '';
    protected bool $required = true;
    protected string $attribute = '';

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
    public function passes(string $attribute, mixed $value): bool
    {
        $this->attribute = $attribute;
        $requiredTranslation = $value[$this->requiredLocale] ?? null;

        if (! empty($requiredTranslation) || is_numeric($requiredTranslation)) {
            return true;
        }

        // If it's not required, we will pass it if the data is empty.
        return ! $this->required && empty($requiredTranslation);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.custom-rules.language-required', [
            'attribute' => Translation::translateAttribute($this->attribute),
            'locale' => __('validation.attributes')[$this->requiredLocale] ?? $this->requiredLocale,
        ]);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}