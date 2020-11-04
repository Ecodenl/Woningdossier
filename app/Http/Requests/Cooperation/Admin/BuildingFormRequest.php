<?php

namespace App\Http\Requests\Cooperation\Admin;

use App\Models\Account;
use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildingFormRequest extends FormRequest
{
    /**
     * @var Account
     */
    private $accountToUpdate = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->accountToUpdate = $this->route('building')->user->account;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accounts.email' => ['required', 'email', Rule::unique('accounts', 'email')->ignore($this->accountToUpdate->id)],
            'users.first_name' => 'required|string|max:255',
            'users.last_name' => 'required|string|max:255',
            'users.phone_number' => ['nullable', new PhoneNumber('nl')],
            'buildings.postal_code' => ['required', new PostalCode('nl')],
            'buildings.number' => ['required', 'integer', new HouseNumber('nl')],
            'buildings.extension' => ['nullable', new HouseNumberExtension('nl')],
            'buildings.street' => 'required|string',
            'buildings.city' => 'required|string',
        ];
    }
}
