<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'questionnaires.steps' => ['required', 'array', 'min:1'],
            'questionnaires.steps.*' => [Rule::exists('steps', 'id')],
            'questionnaires.name.*' => 'required',
        ];
    }
}
