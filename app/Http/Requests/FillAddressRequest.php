<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class FillAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * User may do the request if he is not authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', new HouseNumber('nl'), 'numeric'],
            'extension' => ['nullable'], // Should be returned from the BAG so it doesn't need further validation
        ];
    }
}
