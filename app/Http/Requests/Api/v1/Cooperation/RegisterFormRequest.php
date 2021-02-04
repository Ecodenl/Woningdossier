<?php

namespace App\Http\Requests\Api\v1\Cooperation;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Account;
use App\Models\Cooperation;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;

class RegisterFormRequest extends ApiRequest
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
        $rules = [
            'email' => 'required|string|email|max:255|unique:accounts',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'extra.contact_id' => 'required',
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', 'integer', new HouseNumber('nl')],
            'house_number_extension' => [new HouseNumberExtension('nl')],
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => ['nullable', new PhoneNumber('nl')],
        ];

        // try to get the account
        $account = Account::where('email', $this->get('email'))->first();
        // if the account exists but the user is not associated with the current cooperation
        // then we unset the email and password rule because we dont need to validate them, we handle them in the controller
        if ($account instanceof Account && ! $account->isAssociatedWith($this->route('cooperation'))) {
            unset($rules['email']);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'allow_access.required' => __('auth.register.validation.allow_access'),
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
