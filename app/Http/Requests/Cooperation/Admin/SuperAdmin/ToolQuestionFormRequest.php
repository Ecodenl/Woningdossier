<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use Illuminate\Foundation\Http\FormRequest;

class ToolQuestionFormRequest extends FormRequest
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
            'tool_questions.name.nl' => 'required',
            'tool_questions.help_text.nl' => 'required',
        ];
    }
}
