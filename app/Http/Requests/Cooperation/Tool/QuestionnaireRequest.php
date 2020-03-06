<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Models\Question;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuestionnaireRequest extends FormRequest
{
    protected $redirect;

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
     * Customize the error messages.
     *
     * @return array
     */
    public function attributes()
    {
        $request = $this->request;
        $questions = $request->get('questions');

        $attributes = [];

        if (is_array($questions) && !empty($questions)) {
            foreach ($questions as $questionId => $questionAnswer) {
                $currentQuestion = Question::find($questionId);
                if ($currentQuestion instanceof Question) {
                    // instead of using the array key as name in validation we give a "dynamic" name
                    $attributes['questions.' . $questionId] = "vraag '$currentQuestion->name'";
                }
            }
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
        $this->redirect = url()->previous() . '/' . $this->request->get('tab_id', 'main-tab');

        $questions = $this->get('questions');
        $validationRules = [];

        if (is_array($questions) && !empty($questions)) {
            // loop through the questions
            foreach ($questions as $questionId => $questionAnswer) {
                // get the current question and the validation for that question
                $currentQuestion = Question::find($questionId);

                // if the question is not found, return the user back
                if ($currentQuestion instanceof Question) {
                    $validationRules['questions.' . $questionId] = QuestionnaireService::createValidationRuleForQuestion($currentQuestion);
                }
            }
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
            $questions = $this->get('questions');
            if (is_array($questions) && !empty($questions)) {
                foreach ($questions as $questionId => $questionAnswer) {
                    // check whether the question exists or not.
                    if (!Question::find($questionId) instanceof Question) {
                        $validator->errors()->add('faulty_question', 'Er is iets fout gegaan, vul het formulier opniew in.');
                        Log::debug(__METHOD__ . 'user submitted a custom questionnaire but question was not found.');
                        Log::debug(__METHOD__ . "question_id: {$questionId}");
                        Log::debug(__METHOD__ . "questionnaire_id: {$this->get('questionnaire_id')}");
                    }
                }
            }
        });
    }
}
