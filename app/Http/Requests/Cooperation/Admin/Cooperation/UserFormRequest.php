<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use App\Http\Requests\AddressFormRequest;
use App\Models\Account;
use App\Models\Cooperation;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Logic is in middleware on the routes
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $cooperationToCheckFor = $this->route('cooperationToManage') instanceof Cooperation ? $this->route('cooperationToManage') : $this->route('cooperation');

        $rules = array_merge([
            'accounts.email' => ['required', 'email', Rule::unique('accounts', 'email')],
            'users.first_name' => 'required|string|max:255',
            'users.last_name' => 'required|string|max:255',
            'users.phone_number' => ['nullable', new PhoneNumber('nl')],
            'users.extra.contact_id' => ['nullable', 'numeric', 'integer', 'gt:0'],
            'roles' => 'required|exists:roles,id', // TODO: This doesn't evaluate if the user may assign the role.
            'coach_id' => ['nullable', Rule::exists('users', 'id')],
        ], (new AddressFormRequest())->rules());

        // try to get the account
        $account = Account::where('email', $this->input('accounts.email'))->first();
        // if the account exists but the user is not associated with the current cooperation
        // then just want the email as is.
        if ($account instanceof Account && ! $account->isAssociatedWith($cooperationToCheckFor)) {
            $rules['accounts.email'] = ['required', 'email'];
        }

        return $rules;
    }
}
