<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
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
        return [
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', new HouseNumber('nl'), 'numeric'],
            'extension' => ['nullable', new HouseNumberExtension('nl')],
        ];
    }
}
