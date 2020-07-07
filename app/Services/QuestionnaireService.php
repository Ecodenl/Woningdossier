<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Step;
use App\Models\Translation;
use Ramsey\Uuid\Uuid;

class QuestionnaireService {

    public static function copyQuestionnaireToCooperation(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $cooperationId = $cooperation->id;

        /** @var Questionnaire $questionnaireToReplicate */
        $questionnaireToReplicate = $questionnaire->replicate();

        // for now this will do it as there is only one translation which is dutch.
        // we MUST create a new translation because this will generate a new record in the translations table
        // this way each cooperation can edit the question names without messing up the other cooperation its questionnaires.
        $questionnaireToReplicate->cooperation_id = $cooperationId;
        $questionnaireToReplicate->createTranslations('name', ['nl' => $questionnaire->name]);
        $questionnaireToReplicate->is_active = false;
        $questionnaireToReplicate->save();

        // here we will replicate all the questions with the new translation, questionnaire id and question options.
        foreach ($questionnaire->questions as $question) {

            /** @var Question $questionToReplicate */
            $questionToReplicate = $question->replicate();
            $questionToReplicate->questionnaire_id = $questionnaireToReplicate->id;
            $questionToReplicate->createTranslations('name', ['nl' => $question->name]);
            $questionToReplicate->save();

            // now replicate the question options and change the question id to the replicated question.
            foreach ($question->questionOptions as $questionOption) {
                /** @var QuestionOption $questionOptionToReplicate */
                $questionOptionToReplicate = $questionOption->replicate();
                $questionOptionToReplicate->createTranslations('name', ['nl' => $questionOption->name]);
                $questionOptionToReplicate->question_id = $questionToReplicate->id;
                $questionOptionToReplicate->save();

            }
        }
    }

    /**
     * Method to create a laravel validation rule for a given question.
     *
     * @param Question $question
     *
     * @return array
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
            'nullable'
        ];
        // if its required add the required rule
        if ($question->isRequired()) {
            $rule = [
                'required'
            ];
        }

        foreach ($validation as $mainRule => $rules) {
            // check if there is validation for the question
            if (!empty($validation)) {
                array_push($rule, $mainRule);

                // create the "sub rule"
                foreach ($rules as $subRule => $subRuleCheckValues) {
                    $subRuleProperties = implode($subRuleCheckValues, ',');
                    $subRule = "{$subRule}:$subRuleProperties";
                    array_push($rule, $subRule);
                }
            }
        }

        return $rule;
    }

    /**
     * Method to create a new questionnaire.
     *
     * @param Cooperation $cooperation
     * @param Step $step
     * @param array $questionnaireNameTranslations
     * @return Questionnaire
     * @throws \Exception
     */
    public static function createQuestionnaire(Cooperation $cooperation, Step $step, array $questionnaireNameTranslations): Questionnaire
    {
        $questionnaireNameKey = Uuid::uuid4();

        $maxOrderForQuestionnairesInSelectedSteps = $step->questionnaires()->max('order');

        $questionnaire = Questionnaire::create([
            'name' => $questionnaireNameKey,
            'step_id' => $step->id,
            'order' => ++$maxOrderForQuestionnairesInSelectedSteps,
            'cooperation_id' => $cooperation->id,
            'is_active' => false,
        ]);

        if (self::isNotEmptyTranslation($questionnaireNameTranslations)) {
            foreach ($questionnaireNameTranslations as $locale => $questionnaireNameTranslation) {
                if (empty($questionnaireNameTranslation)) {
                    $questionnaireNameTranslation = current(array_filter($questionnaireNameTranslations));
                }
                Translation::create([
                    'key' =>  $questionnaireNameKey,
                    'language' => $locale,
                    'translation' => $questionnaireNameTranslation,
                ]);
            }
        }
        return $questionnaire;
    }
    /**
     * Determine whether a question has options based on the type
     *
     * @param $questionType
     * @return bool
     */
    public static function hasQuestionOptions($questionType)
    {
        $questionTypeThatHaveOptions = ['select', 'radio', 'checkbox'];

        return in_array($questionType, $questionTypeThatHaveOptions);
    }

    /**
     * Update a questionnaire itself, its name and step.
     *
     * @param Questionnaire $questionnaire
     * @param $questionnaireNameTranslations
     * @param $stepId
     */
    public static function updateQuestionnaire(Questionnaire $questionnaire, $questionnaireNameTranslations, $stepId)
    {
        // update the step
        $questionnaire->update([
            'step_id' => $stepId,
        ]);

        // and update the translations
        foreach ($questionnaireNameTranslations as $locale => $questionnaireNameTranslation) {

            $questionnaireNameTranslation = self::getTranslation($questionnaireNameTranslations, $questionnaireNameTranslation);

            $questionnaire->updateTranslation('name', $questionnaireNameTranslation, $locale);
        }

    }
    /**
     * Method to create a new question for a questionnaire
     *
     * @param Questionnaire $questionnaire
     * @param array $questionData
     * @param string $questionType
     * @param array $validation
     * @param $order
     */
    public static function createQuestion(Questionnaire $questionnaire, array $questionData, string $questionType, array $validation, $order)
    {
        $required = array_key_exists('required', $questionData);
        $uuid = Str::uuid();

        if (self::isNotEmptyTranslation($questionData['question'])) {
            // if the translations are not present, we do not want to create a question
            $createdQuestion = $questionnaire->questions()->create([
                'name' => $uuid,
                'type' => $questionType,
                'order' => $order,
                'required' => $required,
                'validation' => self::getValidationRule($questionData, $validation),
            ]);

            self::createTranslationsForQuestion($questionData['question'], $uuid);

            if (self::hasQuestionOptions($questionType) && $createdQuestion instanceof Question) {
                // create the options for the question
                foreach ($questionData['options'] as $newOptions) {
                    self::createQuestionOptions($newOptions, $createdQuestion);
                }
            }
        }
    }

    /**
     * Create or update an question from a questionnaire
     *
     * @param Questionnaire $questionnaire
     * @param int|string $questionIdOrUuid
     * @param array $questionData
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
     * Method to create translations for a question
     *
     * @param $translationForQuestions
     * @param $translationKey
     */
    public static function createTranslationsForQuestion($translationForQuestions, $translationKey)
    {
        // multiple translations can be available
        foreach ($translationForQuestions as $locale => $translation) {

            $translation = self::getTranslation($translationForQuestions, $translation);

            Translation::create([
                'key' => $translationKey,
                'translation' => $translation,
                'language' => $locale,
            ]);
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
        if (self::isNotEmptyTranslation($newOptions)) {

            $optionNameUuid = Str::uuid();
            // for every option we need to create a option input
            QuestionOption::create([
                'question_id' => $question->id,
                'name' => $optionNameUuid,
            ]);

            // for every translation we need to create a new, you wont guess! Translation.
            self::createTranslationsForQuestion($newOptions, $optionNameUuid);
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

                    $option = self::getTranslation($translations, $option);

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
     * Method to return the translation for an array of translations
     *
     * @param array $translations array of all the translations
     * @param string|null $translation the current translation
     *
     * @return string
     */
    public static function getTranslation(array $translations, $translation)
    {
        // if a translation is empty, try to obtain a other translation.
        // so we never have empty translations for questions
        if (empty($translation)) {
            $translation = current(array_filter($translations));
        }

        return $translation;
    }

    /**
     * Update a question, if the question has options we will update the question options as well.
     *
     * @param int   $questionId
     * @param array $editedQuestion
     * @param array $validation
     */
    public static function updateQuestion(int $questionId, array $editedQuestion, array $validation, $order)
    {
        $required = array_key_exists('required', $editedQuestion);


        $currentQuestion = Question::find($questionId);

        $currentQuestion->update([
            'validation' => self::getValidationRule($editedQuestion, $validation),
            'order' => $order,
            'required' => $required,
        ]);

        if (self::isNotEmptyTranslation($editedQuestion['question'])) {
            // multiple translations can be available
            foreach ($editedQuestion['question'] as $locale => $question) {

                $question = self::getTranslation($editedQuestion['question'], $question);

                $currentQuestion->updateTranslation('name', $question, $locale);
            }
        }

        if (self::hasQuestionOptions($currentQuestion->type)) {
            self::updateQuestionOptions($editedQuestion, $currentQuestion);
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
            if (!empty($translation)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the inverse of isEmptyTranslation.
     *
     * @param array $translations
     *
     * @return bool
     */
    public static function isNotEmptyTranslation(array $translations): bool
    {
        return ! self::isEmptyTranslation($translations);
    }

    /**
     * Returns the validation rule in a array.
     *
     * @param array $questionData
     * @param array $validation
     *
     * @return array
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
     *
     * @param array $questionData
     * @param array $validation
     *
     * @return array
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


}