<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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
