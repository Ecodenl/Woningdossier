<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.questionnaires.index', compact('questionnaires'));
    }

    public function destroy(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $questionnaire->delete();

        return response(200);
    }

    public function edit(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $this->authorize('update', $questionnaire);

        $steps = Step::withoutChildren()->expert()->orderBy('order')->get();

        return view('cooperation.admin.cooperation.questionnaires.questionnaire-editor', compact('questionnaire', 'steps'));
    }

    public function create()
    {
        $this->authorize('create', Questionnaire::class);

        $steps = Step::withoutChildren()->expert()->orderBy('order')->get();

        return view('cooperation.admin.cooperation.questionnaires.create', compact('steps'));
    }

    /**
     * Update the questionnaire and questions
     * if there are new questions create those toes.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(QuestionnaireRequest $request, Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $this->authorize('update', $questionnaire);

        // TODO: Make form name plural, like the table name
        $questionnaireData = $request->validated()['questionnaire'];
        $questionnaire->update($questionnaireData);

        // get the data for the questionnaire
        $validation = $request->get('validation', []);
        $order = 0;

        if ($request->has('questions')) {
            foreach ($request->input('questions') as $questionIdOrUuid => $questionData) {
                ++$order;
                QuestionnaireService::createOrUpdateQuestion($questionnaire, $questionIdOrUuid, $questionData, $validation, $order);
            }
        }

        return redirect()
            ->route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'))
            ->with('success', __('woningdossier.cooperation.admin.cooperation.questionnaires.edit.success'));
    }

    /**
     * Store a questionnaire, after this the user will get redirected to the edit page and he can add questions to the questionnaire.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cooperation $cooperation, QuestionnaireRequest $request)
    {
        $this->authorize('create', Questionnaire::class);

        // TODO: Make form name plural, like the table name
        $questionnaireData = $request->validated()['questionnaire'];

        $nameTranslations = $questionnaireData['name'];
        $stepId = $questionnaireData['step_id'];

        $step = Step::find($stepId);

        QuestionnaireService::createQuestionnaire($cooperation, $step, $nameTranslations);

        return redirect()->route('cooperation.admin.cooperation.questionnaires.index');
    }

    /**
     * Detele a question (softdelete).
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return int
     */
    public function deleteQuestion(Cooperation $cooperation, $questionId)
    {
        $question = Question::find($questionId);

        // since a newly added question that is not saved yet, can still be deleted. If that happens we would get an exception which we dont want
        if ($question instanceof Question) {
            $questionnaire = $question->questionnaire;
            $this->authorize('delete', $questionnaire);

            // rm
            $question->delete();
        }

        return 202;
    }

    /**
     * Delete a question option.
     *
     * @param $questionId
     * @param $questionOptionId
     *
     * @throws \Exception
     *
     * @return int
     */
    public function deleteQuestionOption(Cooperation $cooperation, $questionId, $questionOptionId)
    {
        $question = Question::find($questionId);
        // since a newly added question that is not saved yet, can still be deleted. If that happens we would get an exception which we dont want
        if ($question instanceof Question) {
            $questionnaire = $question->questionnaire;
            $this->authorize('delete', $questionnaire);

            $questionOption = QuestionOption::find($questionOptionId);

            // since the question could exist, but the option dont. So check.
            if ($questionOption instanceof QuestionOption) {
                $questionOption->delete();
            }
        }

        return 202;
    }

    /**
     * Check if the translations from the request are empty.
     *
     * @param $translations
     */
    protected function isEmptyTranslation(array $translations): bool
    {
        foreach ($translations as $locale => $translation) {
            if (! is_null($translation)) {
                return false;
            }
        }

        return true;
    }

    protected function isNotEmptyTranslation(array $translations): bool
    {
        return ! $this->isEmptyTranslation($translations);
    }

    /**
     * Set the active status from a questionnaire.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return mixed
     */
    public function setActive(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');
        $active = $request->get('questionnaire_active');
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('setActiveStatus', $questionnaire);

        if ('true' == $active) {
            $active = true;
        } else {
            $active = false;
        }

        $questionnaire->is_active = $active;
        $questionnaire->save();

        return $questionnaireId;
    }
}
