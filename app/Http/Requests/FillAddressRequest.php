<?php

namespace App\Http\Requests;

use App\Models\Cooperation;
use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class FillAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User may do the request if he is not authorized.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Used in multiple API endpoints. If cooperation is set > we use that. Else we use country
        $cooperation = $this->route('cooperation');
        $country = $cooperation instanceof Cooperation ? $cooperation->country : $this->route('country');

        return [
            'postal_code' => ['required', new PostalCode($country)],
            'number' => ['required', new HouseNumber($country), 'numeric'],
            // If NL, should be returned from the BAG. However, validating is pretty rough... so as long as it's a string?
            // TODO: Should we enhance validation?
            'extension' => ['nullable', 'string'],
        ];
    }
}
