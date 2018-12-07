<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     * Create a question with text as his type
     *
     * @param int $questionnaireId
     * @param array $newQuestion
     */
    protected function createTextQuestion(int $questionnaireId, array $newQuestion)
    {

        $required = false;

        if (array_key_exists('required', $newQuestion)) {
            $required = true;
        }

        $uuid = Str::uuid();

        Question::create([
            'name' => $uuid,
            'type' => 'text',
            'order' => rand(1, 3),
            'required' => $required,
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
    }

    /**
     * Create the options for a question
     *
     * @param array $newQuestion
     * @param Question $createdQuestion
     */
    protected function createQuestionOptions(array $newQuestion, Question $createdQuestion)
    {
        foreach ($newQuestion['options'] as $translations) {
            if (!$this->isEmptyTranslation($translations)) {

                $optionNameUuid = Str::uuid();
                // for every option we need to create a option input
                QuestionOption::create([
                    'question_id' => $createdQuestion->id,
                    'name' => $optionNameUuid,
                ]);

                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($translations as $locale => $translation) {
                    Translation::create([
                        'key' => $optionNameUuid,
                        'translation' => $translation,
                        'language' => $locale
                    ]);
                }
            }
        }
    }

    /**
     * Create a question with select as his type
     *
     * @param int $questionnaireId
     * @param array $newQuestion
     */
    protected function createSelectQuestion(int $questionnaireId, array $newQuestion)
    {
        $required = false;

        if (array_key_exists('required', $newQuestion)) {
            $required = true;
        }

        $questionNameUUid = Str::uuid();

        $createdQuestion = Question::create([
            'name' => $questionNameUUid,
            'type' => 'select',
            'order' => rand(1, 3),
            'required' => $required,
            'questionnaire_id' => $questionnaireId
        ]);

        // multiple translations can be available
        foreach ($newQuestion['question'] as $locale => $question) {
            // the uuid we will put in the key for the translation and set in the question name column-
            Translation::create([
                'key' => $questionNameUUid,
                'translation' => $question,
                'language' => $locale
            ]);
        }

        $this->createQuestionOptions($newQuestion, $createdQuestion);

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

        $currentQuestion = Question::find($questionId);
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
     * Update the options from a question
     *
     * @param Question $currentQuestion
     * @param array $editedQuestion
     */
    public function updateQuestionOptions(Question $currentQuestion, array $editedQuestion)
    {
        foreach ($editedQuestion['options'] as $translations) {
            if (!$this->isEmptyTranslation($translations)) {

                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($translations as $locale => $option) {
                    $currentQuestion->questionOptions()
                        ->where('question_id', $currentQuestion->id)
                        ->first()->updateTranslation('name', $option, $locale);
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

        $this->updateQuestionOptions($currentQuestion, $editedQuestion);
    }

    public function store(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');

        if ($request->has('questions.new')) {
            $newQuestions = $request->input('questions.new');

            foreach ($newQuestions as $newQuestion) {
                $questionType = $newQuestion['type'];

                switch ($questionType) {
                    case ('text'):
                        $this->createTextQuestion($questionnaireId, $newQuestion);
                        break;
                    case('select'):
                        $this->createSelectQuestion($questionnaireId, $newQuestion);
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
    protected function isEmptyTranslation($translations)
    {
        foreach($translations as $locale => $translation) {
            if (!is_null($translation)) {
                return false;
            }
        }
        return true;

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
