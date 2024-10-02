<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'questionnaires.steps' => ['required', 'array', 'min:1'],
            'questionnaires.steps.*' => [Rule::exists('steps', 'id')],
            'questionnaires.name.*' => 'required',
            'validation.*.main-rule' => 'required',
            'validation.*.sub-rule' => 'required',
            'validation.*.sub-rule-check-value.*' => 'required',
        ];
    }
}
