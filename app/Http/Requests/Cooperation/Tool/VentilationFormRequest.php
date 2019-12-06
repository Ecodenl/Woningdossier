<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\Cooperation\Tool\VentilationHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VentilationFormRequest extends FormRequest
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
            'building_ventilations.how' => ['required', 'array', Rule::in(array_keys(VentilationHelper::getHowValues()))],
            'building_ventilations.living_situation' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getLivingSituationValues()))],
            'building_ventilations.usage' => ['nullable', 'array', Rule::in(array_keys(VentilationHelper::getUsageValues()))],
        ];
    }
}
