<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FloorInsulationFormRequest extends FormRequest
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
            'element.*' => 'exists:element_values,id',
            'element.*.extra' => 'nullable|alpha',
            'element.*.element_value_id' => 'exists:element_values,id',
            'element.crawlspace' => 'nullable|alpha',

            'building_features.surface' => 'nullable|numeric',
        ];
    }
}
