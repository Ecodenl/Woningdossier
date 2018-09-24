<?php

namespace App\Http\Requests\Admin\Cooperation\Coordinator;

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
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'password' => 'nullable|min:6',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|exists:roles,id'
        ];
    }
}
