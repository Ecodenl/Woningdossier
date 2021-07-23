<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsFormRequest extends FormRequest
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
            'medias.logo' => 'nullable|image',
            'medias.background' => 'nullable|image'
        ];
    }
}
