<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use App\Rules\LanguageRequired;
use Illuminate\Foundation\Http\FormRequest;

class ToolQuestionFormRequest extends FormRequest
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
            'tool_questions.name' => new LanguageRequired(),
            'tool_questions.help_text' => new LanguageRequired(),
        ];
    }
}
