<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CooperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cooperations.name' => 'required',
            'cooperations.slug' => ['required', Rule::unique('cooperations', 'slug')->ignore($this->route('cooperationToUpdate'))],
            'cooperations.website_url' => 'nullable|url',
            'cooperations.cooperation_email' => 'nullable|email',
            'cooperations.econobis_wildcard' => 'nullable',
        ];
    }
}
