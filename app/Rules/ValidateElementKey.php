<?php

namespace App\Rules;

use App\Helpers\HoomdossierSession;
use App\Models\Element;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class ValidateElementKey implements ValidationRule
{
    protected $elementShort;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $elementShort)
    {
        $this->elementShort = $elementShort;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $element = Element::findByShort($this->elementShort);

        // when this does not exist the user is messing around, return back with no msg.
        if (! array_key_exists($element->id, $value)) {
            $building = HoomdossierSession::getBuilding(true);
            $inputSource = HoomdossierSession::getInputSource(true);
            Log::debug(__METHOD__ . "user is messing around. user_id {$building->user_id} input_source_id: {$inputSource->id}");

            return false;
        }

        return  true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Er is iets fout gegaan, probeer het opnieuw';
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
