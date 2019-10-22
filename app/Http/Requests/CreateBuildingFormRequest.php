<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Building;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class CreateBuildingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        // allowed when there is no building attached
//        return !Account::where('email', $this->get('email'))->first()->user()->building instanceof Building;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:accounts,email',
            'postal_code' => ['required', new PostalCode('nl')],
            'number' => ['required', new HouseNumber('nl')],
            'house_number_extension' => [new HouseNumberExtension('nl')],
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ];
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
