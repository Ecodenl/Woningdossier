<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FloorInsulationFormRequest extends FormRequest
{
    use DecimalReplacementTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function getValidatorInstance()
    {
        $this->decimals(['building_features.floor_surface', 'building_features.insulation_surface']);

        return parent::getValidatorInstance();
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
            'building_features.*' => 'nullable|numeric',
            'building_features.floor_surface' => 'nullable|numeric|min:0',
            'building_features.insulation_surface' => 'nullable|numeric|needs_to_be_lower_or_same_as:building_features.floor_surface',
        ];
    }
}
