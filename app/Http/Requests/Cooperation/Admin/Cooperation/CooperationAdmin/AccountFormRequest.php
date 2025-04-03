<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        /** @var \App\Models\Cooperation $cooperation */
        $cooperation = $this->route('cooperation');

        return [
            'accounts.id' => [
                Rule::exists('accounts', 'id'),
                function ($attribute, $value, $fail) use ($cooperation) {
                    $account = Account::find($value);
                    if ($account instanceof Account) {
                        if ($account->users()->where('cooperation_id', $cooperation->id)->doesntExist()) {
                            $fail('The ' . $attribute . ' is invalid.');
                        }
                    }
                },
            ]
        ];
    }
}
