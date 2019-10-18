<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Step;
use App\Models\Translation;
use App\Services\QuestionnaireService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.questionnaires.index', compact('questionnaires'));
    }

    public function edit(Cooperation $cooperation, $questionnaireId)
    {
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('edit', $questionnaire);

        $steps = Step::orderBy('order')->get();

        return view('cooperation.admin.cooperation.questionnaires.questionnaire-editor', compact('questionnaire', 'steps'));
    }

    public function create()
    {
        $steps = Step::orderBy('order')->get();

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
    public function update(QuestionnaireRequest $request)
    {
        // get the data for the questionnaire
        $questionnaireNameTranslations = $request->input('questionnaire.name');
        $questionnaireId = $request->input('questionnaire.id');
        $validation = $request->get('validation', []);
        $stepId = $request->input('questionnaire.step_id');
        $order = 0;

        // find the current questionnaire
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('update', $questionnaire);

        QuestionnaireService::updateQuestionnaire($questionnaire, $stepId, $questionnaireNameTranslations);

        if ($request->has('questions')) {
            foreach ($request->get('questions') as $questionIdOrUuid => $questionData) {
                ++$order;
                QuestionnaireService::createOrUpdateQuestion($questionnaire, $questionIdOrUuid, $questionData, $validation, $order);
            }
        }

        return redirect(route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire')))
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
    public function store(QuestionnaireRequest $request)
    {
        $this->authorize('store', Questionnaire::class);

        $questionnaireNameKey = Uuid::uuid4();

        $questionnaireNameTranslations = $request->input('questionnaire.name');
        $stepId = $request->input('questionnaire.step_id');

        $step = Step::find($stepId);

        $maxOrderForQuestionnairesInSelectedSteps = $step->questionnaires()->max('order');

        $questionnaire = Questionnaire::create([
            'name' => $questionnaireNameKey,
            'step_id' => $stepId,
            'order' => ++$maxOrderForQuestionnairesInSelectedSteps,
            'cooperation_id' => HoomdossierSession::getCooperation(),
            'is_active' => false,
        ]);

        if ($this->isNotEmptyTranslation($questionnaireNameTranslations)) {
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

        return redirect()->route('cooperation.admin.cooperation.questionnaires.edit', ['id' => $questionnaire->id]);
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
     * @param Cooperation $cooperation
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
                $questionOption->deleteTranslations('name');
                $questionOption->delete();
            }
        }

        return 202;
    }

    /**
     * Check if the translations from the request are empty.
     *
     * @param $translations
     *
     * @return bool
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
     * @param Request $request
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
