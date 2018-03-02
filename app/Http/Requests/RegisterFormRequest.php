<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
	        'email' => 'required|string|email|max:255|unique:users',
	        'password' => 'required|string|min:6',
	        'first_name' => 'required|string|max:255',
	        'last_name' => 'required|string|max:255',
	        'postal_code' => ['required', new PostalCode()],
	        'number' => ['required', new HouseNumber()],
	        'street' => 'required|string|max:255',
	        'city' => 'required|string|max:255',
	        'phone_number' => [ 'nullable', new PhoneNumber() ],
        ];
    }
}
