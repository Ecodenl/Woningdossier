<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class AddressFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address.postal_code' => ['required', new PostalCode('nl')],
            'address.number' => ['required', 'numeric', new HouseNumber('nl')],
            'address.extension' => ['nullable'], // Should be returned from the BAG so it doesn't need further validation
            'address.street' => 'required|string|max:255',
            'address.city' => 'required|string|max:255',
        ];
    }
}