<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use Illuminate\Foundation\Http\FormRequest;

class ToolCalculationResultFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tool_calculation_results.name.nl' => 'required',
            'tool_calculation_results.help_text.nl' => 'required',
            'tool_calculation_results.unit_of_measure' => 'nullable|string|max:256',
        ];
    }
}
