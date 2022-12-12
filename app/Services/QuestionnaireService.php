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
                    if (!empty($subRuleProperties)) {
                        $subRule = "{$subRule}:$subRuleProperties";
                    }
                    array_push($rule, $subRule);
                }
            }
        }

        return $rule;
    }

    /**
     * Determine whether a question has options based on the type.
     *
     * @param $questionType
     *
     * @return bool
     */
    public static function hasQuestionOptions($questionType)
    {
        $questionTypeThatHaveOptions = ['select', 'radio', 'checkbox'];

        return in_array($questionType, $questionTypeThatHaveOptions);
    }

    /**
     * Method to create a new question for a questionnaire.
     *
     * @param $order
     */
    public static function createQuestion(Questionnaire $questionnaire, array $questionData, string $questionType, array $validation, $order)
    {
        $required = array_key_exists('required', $questionData);

        if (self::isNotEmptyTranslation($questionData['question'])) {
            $createdQuestion = $questionnaire->questions()->create([
                'name' => $questionData['question'],
                'type' => $questionType,
                'order' => $order,
                'required' => $required,
                'validation' => self::getValidationRule($questionData, $validation),
            ]);

            if (self::hasQuestionOptions($questionType) && $createdQuestion instanceof Question) {
                // create the options for the question
                foreach ($questionData['options'] as $newOptions) {
                    self::createQuestionOptions($newOptions, $createdQuestion);
                }
            }
        }
    }

    /**
     * Create or update an question from a questionnaire.
     *
     * @param int|string $questionIdOrUuid
     * @param $validation
     * @param $order
     */
    public static function createOrUpdateQuestion(Questionnaire $questionnaire, $questionIdOrUuid, array $questionData, $validation, $order)
    {
        // $questionIdOrUuid is either a guid or a id, when its a guid its a new question otherwise its an existing question and we will update it
        if (Str::isValidGuid($questionIdOrUuid)) {
            self::createQuestion($questionnaire, $questionData, $questionData['type'], $validation, $order);
        } else {
            self::updateQuestion($questionIdOrUuid, $questionData, $validation, $order);
        }
    }

    /**
     * Create the options for a question.
     *
     * Creates question option
     */
    public static function createQuestionOptions(array $newOptions, Question $question)
    {
        if (self::isNotEmptyTranslation($newOptions)) {
            QuestionOption::create([
                'question_id' => $question->id,
                'name' => $newOptions,
            ]);
        }
    }

    /**
     * Update the options from a question.
     *
     * @param Question $question
     */
    public static function updateQuestionOptions(array $editedQuestion, $question)
    {
        // $questionOptionId will mostly contain the id of a QuestionOption
        // however, if a new option to a existing question is added, we set a guid.
        // so if the $questionOptionId = a valid guid we need to create a new QuestionOption.
        foreach ($editedQuestion['options'] as $questionOptionId => $translations) {
            // check whether its a guid
            if (Str::isValidGuid($questionOptionId) && self::isNotEmptyTranslation($translations)) {
                // its a new option, create it
                self::createQuestionOptions($translations, $question);
            } elseif (self::isNotEmptyTranslation($translations)) {
                QuestionOption::find($questionOptionId)->update([
                    'name' => $translations,
                ]);
            }
        }
    }

    /**
     * Update a question, if the question has options we will update the question options as well.
     */
    public static function updateQuestion(int $questionId, array $editedQuestion, array $validation, $order)
    {
        $required = array_key_exists('required', $editedQuestion);

        $currentQuestion = Question::find($questionId);

        $data = [
            'validation' => self::getValidationRule($editedQuestion, $validation),
            'order' => $order,
            'required' => $required,
        ];

        if (self::isNotEmptyTranslation($editedQuestion['question'])) {
            $data['name'] = $editedQuestion['question'];
        }

        $currentQuestion->update($data);

        if (self::hasQuestionOptions($currentQuestion->type)) {
            self::updateQuestionOptions($editedQuestion, $currentQuestion);
        }
    }

    /**
     * Returns the validation rule in a array.
     */
    public static function getValidationRule(array $questionData, array $validation): array
    {
        // get the validation for the current question
        $validationForCurrentQuestion = self::getValidationForCurrentQuestion($questionData, $validation);

        if (! empty($validationForCurrentQuestion)) {
            // built the validation rule array
            $validationRule = [
                $validationForCurrentQuestion['main-rule'] => [
                    $validationForCurrentQuestion['sub-rule']  => [],
                ],
            ];

            // first check if there are sub rule check values
            if (array_key_exists('sub-rule-check-value', $validationForCurrentQuestion)) {
                // if so, push them inside the sub-rule array
                foreach ($validationForCurrentQuestion['sub-rule-check-value'] as $subRuleCheckValue) {
                    array_push($validationRule[$validationForCurrentQuestion['main-rule']][$validationForCurrentQuestion['sub-rule']], $subRuleCheckValue);
                }
            }

            return $validationRule;
        }

        return [];
    }

    /**
     * Return the validation for the current question.
     */
    public static function getValidationForCurrentQuestion(array $questionData, array $validation): array
    {
        // first check if the question has a guid and check if the guid exists in the validation
        if (array_key_exists('guid', $questionData) && array_key_exists($questionData['guid'], $validation)) {
            return $validation[$questionData['guid']];
        } elseif (array_key_exists('question_id', $questionData) && array_key_exists($questionData['question_id'], $validation)) {
            return $validation[$questionData['question_id']];
        }

        return [];
    }

    /**
     * Returns the inverse of isEmptyTranslation.
     *
     * @param  array  $translations
     *
     * @return bool
     */
    public static function isNotEmptyTranslation(array $translations): bool
    {
        return ! self::isEmptyTranslation($translations);
    }

    /**
     * Check if the translations from the request are empty.
     *
     * @param  array  $translations
     *
     * @return bool
     */
    public static function isEmptyTranslation(array $translations): bool
    {
        foreach ($translations as $locale => $translation) {
            if (! empty($translation)) {
                return false;
            }
        }

        return true;
    }
}
