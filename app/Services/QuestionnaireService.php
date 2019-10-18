<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Translation;

class QuestionnaireService {

    public static function createQuestion(int $questionnaireId, array $requestQuestion, string $questionType, array $validation, $order, bool $questionHasOptions = false)
    {
        $required = false;

        if (array_key_exists('required', $requestQuestion)) {
            $required = true;
        }

        $uuid = Str::uuid();

        if (self::isNotEmptyTranslation($requestQuestion['question'])) {
            // if the translations are not present, we do not want to create a question
            $createdQuestion = Question::create([
                'name' => $uuid,
                'type' => $questionType,
                'order' => $order,
                'required' => $required,
                'validation' => self::getValidationRule($requestQuestion, $validation),
                'questionnaire_id' => $questionnaireId,
            ]);

            // multiple translations can be available
            foreach ($requestQuestion['question'] as $locale => $question) {
                // the uuid we will put in the key for the translation and set in the question name column

                if (empty($question)) {
                    $question = current(array_filter($requestQuestion['question']));
                }

                Translation::create([
                    'key' => $uuid,
                    'translation' => $question,
                    'language' => $locale,
                ]);
            }

            if ($questionHasOptions && $createdQuestion instanceof Question) {
                // create the options for the question
                foreach ($requestQuestion['options'] as $newOptions) {
                    self::createQuestionOptions($newOptions, $createdQuestion);
                }
            }
        }
    }

    /**
     * Create the options for a question.
     *
     * Creates question option and 2 translations
     *
     * @param array    $newOptions
     * @param Question $question
     */
    public static function createQuestionOptions(array $newOptions, Question $question)
    {
        if (! self::isEmptyTranslation($newOptions)) {
            $optionNameUuid = Str::uuid();
            // for every option we need to create a option input
            QuestionOption::create([
                'question_id' => $question->id,
                'name' => $optionNameUuid,
            ]);

            if (self::isNotEmptyTranslation($newOptions)) {
                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($newOptions as $locale => $translation) {
                    if (empty($translation)) {
                        $translation = current(array_filter($newOptions));
                    }

                    Translation::create([
                        'key' => $optionNameUuid,
                        'translation' => $translation,
                        'language' => $locale,
                    ]);
                }
            }
        }
    }

    /**
     * Update a question, if the question has options we will update the question options as well.
     *
     * @param int   $questionId
     * @param array $editedQuestion
     * @param array $validation
     * @param bool  $questionHasOptions
     */
    public static function updateQuestion(int $questionId, array $editedQuestion, array $validation, $order, bool $questionHasOptions = false)
    {
        $required = false;

        if (array_key_exists('required', $editedQuestion)) {
            $required = true;
        }

        $currentQuestion = Question::find($questionId);

        $currentQuestion->update([
            'validation' => self::getValidationRule($editedQuestion, $validation),
            'order' => $order,
            'required' => $required,
        ]);

        if (self::isNotEmptyTranslation($editedQuestion['question'])) {
            // multiple translations can be available
            foreach ($editedQuestion['question'] as $locale => $question) {
                if (empty($question)) {
                    $question = current(array_filter($editedQuestion['question']));
                }
                $currentQuestion->updateTranslation('name', $question, $locale);
            }
        }

        if ($questionHasOptions) {
            self::updateQuestionOptions($editedQuestion, $currentQuestion);
        }
    }


    /**
     * Update the options from a question.
     *
     * @param array    $editedQuestion
     * @param Question $question
     */
    public static function updateQuestionOptions(array $editedQuestion, $question)
    {

        // we will store the new options for the question here.
        $allNewOptions = [];

        // $questionOptionId will mostly contain the id of a QuestionOption
        // however, if a new option to a existing question is added, we set a guid.
        // so if the $questionOptionId = a valid guid we need to create a new QuestionOption and the translation for it.
        foreach ($editedQuestion['options'] as $questionOptionId => $translations) {
            // check whether its a guid and its not empty
            if (Str::isValidGuid($questionOptionId) && self::isNotEmptyTranslation($translations)) {
                // its a new option, add it to the array
                $allNewOptions[$questionOptionId] = $translations;

            } elseif (self::isNotEmptyTranslation($translations)) {
                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($translations as $locale => $option) {
                    if (empty($option)) {
                        $option = current(array_filter($translations));
                    }
                    QuestionOption::find($questionOptionId)->updateTranslation('name', $option, $locale);
                }
            }
        }

        // add the options
        foreach ($allNewOptions as $newOptions) {
            self::createQuestionOptions($newOptions, $question);
        }
    }




    /**
     * Check if the translations from the request are empty.
     *
     * @param $translations
     *
     * @return bool
     */
    public static function isEmptyTranslation(array $translations): bool
    {
        foreach ($translations as $locale => $translation) {
            if (! is_null($translation)) {
                return false;
            }
        }

        return true;
    }

    public static function isNotEmptyTranslation(array $translations): bool
    {
        return ! self::isEmptyTranslation($translations);
    }

    /**
     * Returns the validation rule in a array.
     *
     * @param array $requestQuestion
     * @param array $validation
     *
     * @return array
     */
    public static function getValidationRule(array $requestQuestion, array $validation): array
    {
        // get the validation for the current question
        $validationForCurrentQuestion = self::getValidationForCurrentQuestion($requestQuestion, $validation);

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
     *
     * @param array $requestQuestion
     * @param array $validation
     *
     * @return array
     */
    public static function getValidationForCurrentQuestion(array $requestQuestion, array $validation): array
    {
        // first check if the requestquestion has a guid
        if (array_key_exists('guid', $requestQuestion)) {
            // after that check if the guid exists in the validation
            if (array_key_exists($requestQuestion['guid'], $validation)) {
                return $validation[$requestQuestion['guid']];
            }
        } elseif (array_key_exists('question_id', $requestQuestion)) {
            if (array_key_exists($requestQuestion['question_id'], $validation)) {
                return $validation[$requestQuestion['question_id']];
            }
        }

        return [];
    }


}