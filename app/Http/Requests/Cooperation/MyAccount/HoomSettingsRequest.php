<?php

namespace App\Http\Requests\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Rules\HashCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HoomSettingsRequest extends FormRequest
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
            'account.email' => ['required', 'email', Rule::unique('accounts', 'email')->ignore(Hoomdossier::account()->id)],
            'account.current_password' => [new HashCheck(Hoomdossier::account()->password)],
            'account.password' => 'required_with:account.current_password|nullable|string|confirmed|min:6',
        ];
    }
}
