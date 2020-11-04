<?php

namespace App\Rules;

use App\Helpers\HoomdossierSession;
use App\Models\Element;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidateElementKey implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $element = Element::findByShort($this->elementShort);

        // when this does not exist the user is messing around, return back with no msg.
        if (!array_key_exists($element->id, $value)) {
            $building = HoomdossierSession::getBuilding(true);
            $inputSource = HoomdossierSession::getInputSource(true);
            Log::debug(__METHOD__."user is messing around. user_id {$building->user_id} input_source_id: {$inputSource->id}");
            return false;
        }
        return  true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Er is iets fout gegaan, probeer het opnieuw';
    }
}
