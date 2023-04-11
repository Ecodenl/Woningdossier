<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Models\CooperationSettingHelper;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountFormRequest extends FormRequest
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
            'accounts.id' => [
                Rule::exists('accounts', 'id'),
                function ($attribute, $value, $fail) {
                    $account = Account::find($value);
                    if ($account instanceof Account) {
                        if ($account->users()->where('cooperation_id', $this->route('cooperation')->id)->doesntExist()) {
                            $fail('The '.$attribute.' is invalid.');
                        }
                    }
                },
            ]
        ];
    }
}
