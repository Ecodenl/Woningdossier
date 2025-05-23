<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use App\Rules\LanguageRequired;
use Illuminate\Foundation\Http\FormRequest;

class ToolCalculationResultFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tool_calculation_results.name' => new LanguageRequired(),
            'tool_calculation_results.help_text' => new LanguageRequired(),
        ];
    }
}
