<?php

namespace App\Http\Requests\Cooperation\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ConfirmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !\Auth::check();
    }

    public function prepareForValidation()
    {
        // if this fails the user is doing something fishy.
        $this->redirect = route('cooperation.welcome');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'u' => [
                'required',
                'email',
                Rule::exists('accounts', 'email')->where(function ($query) {
                    $query->where('confirm_token', '=', $this->get('t'));
                }),
            ],
            't' => [
                'required',
                'alpha_num',
                Rule::exists('accounts', 'confirm_token')->where(function ($query) {
                    $query->where('email', '=', $this->get('u'));
                })
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::debug('The confirm account failed, email: '. $this->get('u').' token: '. $this->get('t'));
        parent::failedValidation($validator);
    }
}
