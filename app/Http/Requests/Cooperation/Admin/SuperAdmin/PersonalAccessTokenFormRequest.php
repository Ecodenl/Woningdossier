<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class PersonalAccessTokenFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'personal_access_tokens.name' => 'required|max:256'
        ];
    }
}
