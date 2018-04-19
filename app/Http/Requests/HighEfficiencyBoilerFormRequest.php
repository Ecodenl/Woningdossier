<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HighEfficiencyBoilerFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'habit.*' => 'required|numeric',
            'building_services.*.service_value_id' => 'exists:service_values,id',
            'building_services.*.extra' => 'required|numeric|digits:4',
        ];
    }
}
