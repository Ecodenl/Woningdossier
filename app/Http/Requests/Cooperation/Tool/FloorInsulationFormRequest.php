<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use App\Rules\ValidateElementKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        $noDatabaseSelectOptions = ['yes', 'no', 'unknown'];
        return [
            'element' => ['exists:element_values,id', new ValidateElementKey('floor-insulation')],
            'building_elements.extra.access' => ['nullable', 'alpha', Rule::in($noDatabaseSelectOptions)],
            'building_elements.extra.has_crawlspace' => ['nullable', 'alpha', Rule::in($noDatabaseSelectOptions)],
            'building_elements.element_value_id' => 'exists:element_values,id',
            'building_features.floor_surface' => 'nullable|numeric|min:1|max:100000',
            'building_features.insulation_surface' => 'nullable|numeric|min:0|needs_to_be_lower_or_same_as:building_features.floor_surface',
        ];
    }
}
