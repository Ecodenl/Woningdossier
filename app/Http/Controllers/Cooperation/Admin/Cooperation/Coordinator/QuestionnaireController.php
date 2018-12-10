<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\Str;
use App\Helpers\TranslatableTrait;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.index', compact('questionnaires'));
    }

    public function edit(Cooperation $cooperation, $questionnaireId)
    {
        $questionnaire = Questionnaire::find($questionnaireId);
	    $steps = Step::orderBy('order')->get();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.questionnaire-editor', compact('questionnaire', 'steps'));
    }

    public function create()
    {
	    $steps = Step::orderBy('order')->get();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.create', compact('steps'));
    }

    /**
     * Create a question
     *
     * @param int $questionnaireId
     * @param array $newQuestion
     * @param string $questionType
     * @param bool $questionHasOptions
     */
    protected function createQuestion(int $questionnaireId, array $newQuestion, string $questionType, bool $questionHasOptions = false)
    {

        $required = false;

        if (array_key_exists('required', $newQuestion)) {
            $required = true;
        }

        $uuid = Str::uuid();

        $createdQuestion = Question::create([
            'name' => $uuid,
            'type' => $questionType,
            'order' => rand(1, 3),
            'required' => $required,
            'validation' => $this->getValidationRule($newQuestion),
            'questionnaire_id' => $questionnaireId
        ]);

        // multiple translations can be available
        foreach ($newQuestion['question'] as $locale => $question) {
            // the uuid we will put in the key for the translation and set in the question name column-
            Translation::create([
                'key' => $uuid,
                'translation' => $question,
                'language' => $locale
            ]);
        }

        if ($questionHasOptions) {
            // create the options for the question
            foreach ($newQuestion['options'] as $newOptions) {
                $this->createQuestionOptions($newOptions, $createdQuestion);
            }
        }


    }

    /**
     * Returns the full concatenated validation rule
     *
     * @param array $newQuestion
     * @return string $validationRule
     */
//    protected function getValidationRule(array $newQuestion) : string
//    {
//        // the main validation name
//        $mainValidation = $newQuestion['validation'];
//
//        // example:
//        // $mainValidation = number
//        // $subValidationRule = between
//        // $subValidationRuleName = min or max etc,
//        // $subValidationRuleValue = the value to check the validation on
//
//        $subValidationRule = key($newQuestion['validation-options']);
//
//        $validationRule = "{$mainValidation}|";
//
//        switch ($subValidationRule) {
//            case 'between':
//                $validationRule .= "{$subValidationRule}:";
//                foreach ($newQuestion['validation-options'][$subValidationRule] as $subValidationRuleName => $subValidationRuleValue) {
//                    $validationRule .= "{$subValidationRuleValue},";
//                }
//
//                break;
//        }
//
//        return rtrim($validationRule, ',');
//
//    }
    protected function getValidationRule(array $newQuestion) : array
    {
        // the main validation name
        $mainValidation = $newQuestion['validation'];

        // example:
        // $mainValidation = number
        // $subValidationRule = between
        // $subValidationRuleName = min or max etc,
        // $subValidationRuleValue = the value to check the validation on

        $subValidationRule = key($newQuestion['validation-options']);


        // create an array of the rules
        $validationRule = [
            $mainValidation => [
                $subValidationRule => $newQuestion['validation-options'][$subValidationRule]
            ]
        ];


        return $validationRule;

    }


    /**
     * Update a question with type text
     *
     * @param int $questionId
     * @param array $editedQuestion
     */
    public function updateTextQuestion(int $questionId, array $editedQuestion)
    {
        $required = false;

        if (array_key_exists('required', $editedQuestion)) {
            $required = true;
        }

        $currentQuestion = Question::withTrashed()->find($questionId);

        $currentQuestion->update([
            'type' => 'text',
            'order' => rand(1, 3),
            'required' => $required,
        ]);


        // multiple translations can be available
        foreach ($editedQuestion['question'] as $locale => $question) {
            $currentQuestion->updateTranslation('name', $question, $locale);
        }
    }

    /**
     * Create the options for a question
     *
     * Creates question option and 2 translations
     *
     * @param array $newOptions
     * @param Question $question
     */
    protected function createQuestionOptions(array $newOptions, Question $question)
    {
        if (!$this->isEmptyTranslation($newOptions)) {

            $optionNameUuid = Str::uuid();
            // for every option we need to create a option input
            QuestionOption::create([
                'question_id' => $question->id,
                'name' => $optionNameUuid,
            ]);

            // for every translation we need to create a new, you wont guess! Translation.
            foreach ($newOptions as $locale => $translation) {
                Translation::create([
                    'key' => $optionNameUuid,
                    'translation' => $translation,
                    'language' => $locale
                ]);
            }
        }
    }


    /**
     * Update the options from a question
     *
     * @param array $editedQuestion
     * @param Question $question
     */
    public function updateQuestionOptions(array $editedQuestion, $question)
    {
        // $questionOptionId will mostly contain the id of a QuestionOption
        // however, if a new option to a existing question is added, we set a uuid.
        // so if the $questionOptionId = a valid uuid we need to create a new QuestionOption and the translations.
        foreach ($editedQuestion['options'] as $questionOptionId => $translations) {

            if (Uuid::isValid($questionOptionId) && $this->isNotEmptyTranslation($translations)) {

                // if the uuid is valid a pomp it to a array and create new question options
                $allNewOptions = collect($editedQuestion['options'])->filter(function ($value, $key) {
                    return Uuid::isValid($key);
                })->toArray();

                // create the options
                foreach ($allNewOptions as $newOptions) {
                    $this->createQuestionOptions($newOptions, $question);
                }

            } elseif ($this->isNotEmptyTranslation($translations)) {
                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($translations as $locale => $option) {
                    QuestionOption::find($questionOptionId)->updateTranslation('name', $option, $locale);
                }
            }
        }
    }

    /**
     * Update the question with type select
     *
     * @param $questionId
     * @param $editedQuestion
     */
    public function updateSelectQuestion($questionId, $editedQuestion)
    {
        $required = false;

        if (array_key_exists('required', $editedQuestion)) {
            $required = true;
        }

        $currentQuestion = Question::find($questionId);

        $currentQuestion->update([
            'type' => 'select',
            'required' => $required,
        ]);

        // multiple translations can be available
        foreach ($editedQuestion['question'] as $locale => $question) {
            // the uuid we will put in the key for the translation and set in the question name column-
            $currentQuestion->updateTranslation('name', $question, $locale);
        }

        $this->updateQuestionOptions($editedQuestion, $currentQuestion);
    }

    /**
     * Save the questionnaire, store and update.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

//        dd($request->all());
        $questionnaireId = $request->get('questionnaire_id');

        if ($request->has('questions.new')) {
            $newQuestions = $request->input('questions.new');

            foreach ($newQuestions as $newQuestion) {
                $questionType = $newQuestion['type'];

                switch ($questionType) {
                    case ('text'):
                        $this->createQuestion($questionnaireId, $newQuestion, $questionType);
                        break;
                    case('select'):
                        $this->createQuestion($questionnaireId, $newQuestion, $questionType, true);
                }
            }
        }

        if ($request->has('questions.edit')) {
            $editedQuestions = $request->input('questions.edit');

            foreach ($editedQuestions as $questionId => $editedQuestion) {
                $editedQuestionType = $editedQuestion['type'];

                switch ($editedQuestionType) {
                    case ('text'):
                        $this->updateTextQuestion($questionId, $editedQuestion);
                        break;
                    case ('select'):
                        $this->updateSelectQuestion($questionId, $editedQuestion);
                        break;
                }
            }
        }

        return redirect()
            ->route('cooperation.admin.cooperation.coordinator.questionnaires.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.edit.success'));
    }

    /**
     * Check if the translations from the request are empty
     *
     * @param $translations
     * @return bool
     */
    protected function isEmptyTranslation(array $translations) : bool
    {
        foreach($translations as $locale => $translation) {
            if (!is_null($translation)) {
                return false;
            }
        }
        return true;
    }

    protected function isNotEmptyTranslation(array $translations) : bool
    {
        return !$this->isEmptyTranslation($translations);
    }


    /**
     * Set a questionnaire active status
     *
     * @param Request $request
     */
    public function setActive(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');
        $active = $request->get('questionnaire_active');

        if ($active == "true") {
            $active = true;
        } else {
            $active = false;
        }

        $questionnaire = Questionnaire::find($questionnaireId);
        $questionnaire->is_active = $active;
        $questionnaire->save();

    }
}
