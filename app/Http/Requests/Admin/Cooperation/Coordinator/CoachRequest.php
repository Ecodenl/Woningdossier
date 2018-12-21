<?php

namespace App\Http\Requests\Admin\Cooperation\Coordinator;

use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;

class CoachRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        return \Auth::check();
        return \Auth::check();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // collecting the rules for the fields that need to be required if the role field contains value 5 / resident
        // finding a cleaner way would be nice
        $postalCodeRule = [];
        if (count($this->request->get('roles')) > 1) {
            foreach ($this->request->get('roles') as $inputKey => $roleId) {
                array_push($postalCodeRule, 'required_if:roles.'.$inputKey.',5');
            }
        } else {
             array_push($postalCodeRule, 'required_if:roles.0,5');
             array_push($postalCodeRule, new PostalCode);
        }

        $houseNumberRule = [];
        if (count($this->request->get('roles')) > 1) {
            foreach ($this->request->get('roles') as $inputKey => $roleId) {
                array_push($houseNumberRule, 'required_if:roles.'.$inputKey.',5');
            }
        } else {
             array_push($houseNumberRule, 'required_if:roles.0,5');
             array_push($houseNumberRule, new HouseNumber);
        }

        $roleRequiredIfRule = [];
        if (count($this->request->get('roles')) > 1) {
            foreach ($this->request->get('roles') as $inputKey => $roleId) {
                array_push($roleRequiredIfRule, 'required_if:roles.'.$inputKey.',5');
            }
        } else {
             array_push($roleRequiredIfRule, 'required_if:roles.0,5');
        }

        return [
            'first_name' => ['required', new AlphaSpace()],
            'last_name' => ['required', new AlphaSpace()],
            'password' => 'nullable|min:6',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|exists:roles,id',

            'postal_code' => $postalCodeRule ,
            'number' => $houseNumberRule,
            'street' => $roleRequiredIfRule,
            'city' => $roleRequiredIfRule

        ];
    }
}
