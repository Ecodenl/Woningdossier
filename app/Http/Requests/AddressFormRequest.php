<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class AddressFormRequest extends FormRequest
{
    protected string $country;

    public function setCountry(string $countryIso3166alpha2): self
    {
        //TODO: Just enhancing this FormRequest since we currently only
        // use it as DRY instance for the rules, should we edit? Perhaps prepareForValidation?
        $this->country = $countryIso3166alpha2;
        return $this;
    }

    public function rules(): array
    {
        return [
            'address.postal_code' => ['required', new PostalCode($this->country)],
            'address.number' => ['required', 'numeric', new HouseNumber($this->country)],
            // If NL, should be returned from the BAG. However, validating is pretty rough... so as long as it's a string?
            // TODO: Should we enhance validation?
            'address.extension' => ['nullable', 'string'],
            'address.street' => 'required|string|max:255',
            'address.city' => 'required|string|max:255',
        ];
    }
}
