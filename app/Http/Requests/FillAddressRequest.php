<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
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
        return !\Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => ['required', new HouseNumber('nl')],
        ];
    }
}
