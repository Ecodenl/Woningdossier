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
        return \Auth::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cooperationId = $this->request->get('cooperation_id');
        return [
            'name' => 'required',
            'slug' => [Rule::unique('cooperations', 'slug')->ignore($cooperationId)]
        ];
    }
}
