<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use App\Rules\AlphaSpace;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', new HouseNumber('nl')],
            'house_number_extension' => [new HouseNumberExtension('nl')],
            'street' => 'required|string',
            'city' => 'required|string',
            'roles' => 'required|exists:roles,id',
            'coach_id' => ['nullable', Rule::exists('users', 'id')],
        ];
    }

    /**
     * so fields can be modified or added before validation.
     */
    public function prepareForValidation()
    {
        // Add new data field before it gets sent to the validator
        $this->merge([
            'house_number_extension' => strtolower(preg_replace("/[\s-]+/", '', $this->get('house_number_extension', ''))),
        ]);
    }
}
