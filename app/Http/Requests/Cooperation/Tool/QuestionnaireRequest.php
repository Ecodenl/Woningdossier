<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Models\Question;
use App\Models\Questionnaire;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

class QuestionnaireRequest extends FormRequest
{
    protected $redirect;

    protected Collection $questions;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function prepareForValidation()
    {
        $this->questions = Questionnaire::find($this->input('questionnaire_id'))->questions;
        $this->redirect = url()->previous();
    }

    /**
     * Customize the error messages.
     *
     * @return array
     */
    public function attributes()
    {
        $questions = $this->questions;

        $attributes = [];

        foreach ($questions as $question) {
            // instead of using the array key as name in validation we give a "dynamic" name
            $attributes['questions.'.$question->id] = "vraag '{$question->name}'";
        }

        return $attributes;
    }

    /**
     * Make the rules for the questions.
     *
     * @return array
     */
    public function makeRules()
    {
        $validationRules = [];

        // loop through the questions
        foreach ($this->questions as $question) {
            $validationRules['questions.'.$question->id] = QuestionnaireService::createValidationRuleForQuestion($question);
        }

        return $validationRules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->makeRules();
    }

    public function withValidator(Validator $validator)
    {
        // Check if a question exists in the database
        // for ex; a user can be filling a questionnaire its question while its removed in the backend
        // this wont happen much, but it can happen.
        $validator->after(function ($validator) {
            $questions = $this->input('questions');
            if (is_array($questions) && ! empty($questions)) {
                foreach ($questions as $questionId => $questionAnswer) {
                    // check whether the question exists or not.
                    if (! Question::find($questionId) instanceof Question) {
                        $validator->errors()->add('faulty_question', 'Er is iets fout gegaan, vul het formulier opniew in.');
                        Log::debug(__METHOD__.'user submitted a custom questionnaire but question was not found.');
                        Log::debug(__METHOD__."question_id: {$questionId}");
                        Log::debug(__METHOD__."questionnaire_id: {$this->get('questionnaire_id')}");
                    }
                }
            }
        });
    }
}
