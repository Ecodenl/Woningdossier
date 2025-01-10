<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Step;

class QuestionnaireService
{
    // TODO: This is not a service. Should be refactored

    public static function copyQuestionnaireToCooperation(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $cooperationId = $cooperation->id;

        $questionnaireToReplicate = $questionnaire->replicate();

        $questionnaireToReplicate->cooperation_id = $cooperationId;
        $questionnaireToReplicate->is_active = false;
        $questionnaireToReplicate->save();

        foreach ($questionnaire->questionnaireSteps as $questionnaireStep) {
            $questionnaireStepReplicate = $questionnaireStep->replicate();
            $questionnaireStepReplicate->questionnaire_id = $questionnaireToReplicate->id;
            // TODO: Simple order for now, should we be more complex?
            $questionnaireStepReplicate->order = ++$questionnaireStep->order;
            $questionnaireStepReplicate->save();
        }

        // here we will replicate all the questions with the new questionnaire id and question options.
        foreach ($questionnaire->questions as $question) {
            /** @var Question $questionToReplicate */
            $questionToReplicate = $question->replicate();
            $questionToReplicate->questionnaire_id = $questionnaireToReplicate->id;
            $questionToReplicate->save();

            // now replicate the question options and change the question id to the replicated question.
            foreach ($question->questionOptions as $questionOption) {
                /** @var QuestionOption $questionOptionToReplicate */
                $questionOptionToReplicate = $questionOption->replicate();
                $questionOptionToReplicate->question_id = $questionToReplicate->id;
                $questionOptionToReplicate->save();
            }
        }
    }

    /**
     * Method to create a laravel validation rule for a given question.
     */
    public static function createValidationRuleForQuestion(Question $question): array
    {
        $validation = $question->validation;

        // built the validation
        // nullable is still needed, in some cases the strings will be converted to null
        // if that happens sometimes would not work
        // see ConvertEmptyStringsToNull middleware class
        $rule = [
            'sometimes',
            'nullable',
        ];
        // if its required add the required rule
        if ($question->isRequired()) {
            $rule = [
                'required',
            ];
        }

        foreach ($validation as $mainRule => $rules) {
            // check if there is validation for the question
            if (! empty($validation)) {
                array_push($rule, $mainRule);

                // create the "sub rule"
                foreach ($rules as $subRule => $subRuleCheckValues) {
                    $subRuleProperties = implode(',', $subRuleCheckValues);
                    // ex; max:200, min:100.
                    if (! empty($subRuleProperties)) {
                        $subRule = "{$subRule}:$subRuleProperties";
                    }
                    array_push($rule, $subRule);
                }
            }
        }

        return $rule;
    }
}
