<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\User;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $rules = [
            'email' => 'required|string|email|max:255|unique:accounts',
            'password' => 'required|string|confirmed|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', new HouseNumber('nl')],
            'house_number_extension' => [new HouseNumberExtension('nl')],
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => ['nullable', new PhoneNumber('nl')],
        ];

        // try to get the account
        $account = Account::where('email', $this->get('email'))->first();
        // if the account exists but the user is not associated with the current cooperation
        // then we unset the email and password rule because we dont need to validate them, we handle them in the controller
        if ($account instanceof Account && !$account->isAssociatedWith($this->route('cooperation'))) {
            unset($rules['email'], $rules['password']);
        }

        return $rules;

    }

    /**
     * Extend the default getValidatorInstance method
     * so fields can be modified or added before validation.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // Add new data field before it gets sent to the validator
        //$this->merge(array('date_of_birth' => 'test'));
        $this->merge([
            'house_number_extension' => strtolower(preg_replace("/[\s-]+/", '', $this->get('house_number_extension', ''))),
        ]);

        // Replace ALL data fields before they're sent to the validator
        //$this->replace([
        //	'house_number_extension' => strtolower(preg_replace("/[\s-]+/", "", $this->get('house_number_extension', ''))),
        //]);

        // Fire the parent getValidatorInstance method
        return parent::getValidatorInstance();
    }
}
