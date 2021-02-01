<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use App\Rules\LanguageRequired;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
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
            'questionnaire.step_id' => ['required', Rule::exists('steps', 'id')],
            'questionnaire.name' => [new LanguageRequired('nl')],
            'validation.*.main-rule' => 'required',
            'validation.*.sub-rule' => 'required',
            'validation.*.sub-rule-check-value.*' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questions = $this->request->get('questions');

            if (!empty($questions))
            {
                foreach ($questions as $id => $question)
                {
                    // We check if the question can have options
                    if (QuestionnaireService::hasQuestionOptions($question['type']))
                    {
                        // We get the locales for the question
                        $locales = array_keys($question['question']);

                        foreach ($locales as $locale)
                        {
                            // We set the field of the question
                            $field = "questions.{$id}.question.{$locale}";
                            // If the option is empty, we error, because at least one question is required
                            if (empty($question['options'])) {
                                $validator->errors()->add($field, __('validation.custom.questionnaires.not_enough_options',
                                    ['attribute' => $question['question'][$locale]]));
                            }
                            else
                            {
                                // If the options are set, we check to ensure they are not empty for each locale
                                foreach ($question['options'] as $uuid => $localeOption)
                                {
                                    $field = "questions.{$id}.options.{$uuid}.{$locale}";
                                    if (empty ($localeOption[$locale])) {
                                        $validator->errors()->add($field, __('validation.custom.questionnaires.empty_option',
                                            ['attribute' => $question['question'][$locale], 'locale' => $locale]));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
