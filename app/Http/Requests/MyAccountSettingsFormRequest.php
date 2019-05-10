<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyAccountSettingsFormRequest extends FormRequest
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
            'user.password' => 'nullable|string|confirmed|min:6',
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
//            'user.email' => ['required', 'email', Rule::unique('users', 'email')->ignore(\Auth::id())],
            'user.phone_number' => ['nullable', new PhoneNumber()],


            'building.postal_code' => ['required', new PostalCode('nl')],
            'building.house_number' => ['required', new HouseNumber('nl')],
            'building.house_number_extension' => ['nullable', new HouseNumberExtension('nl')],
            'building.street' => 'required|string|max:255',
            'building.city' => 'required|string|max:255',

        ];
    }
}
