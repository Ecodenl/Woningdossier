<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Arr;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionnaireStep;
use App\Models\QuestionOption;
use App\Models\Scan;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.questionnaires.index', compact('questionnaires'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Questionnaire::class);

        $scans = Scan::with(['steps' => function ($query) {
            $query->whereNotIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater']);
        }])->get();

        return view('cooperation.admin.cooperation.questionnaires.create', compact('scans'));
    }

    /**
     * Store a questionnaire, after this the user will get redirected to the edit page and he can add questions to the questionnaire.
     *
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Cooperation $cooperation, QuestionnaireRequest $request): RedirectResponse
    {
        $this->authorize('create', Questionnaire::class);

        $questionnaireData = $request->validated()['questionnaires'];

        $questionnaire = Questionnaire::create([
            'name' => $questionnaireData['name'],
            'cooperation_id' => $cooperation->id,
            'is_active' => false,
        ]);

        $steps = Step::findMany($questionnaireData['steps']);

        $stepIds = [];
        foreach ($steps as $step) {
            $orderForStep = QuestionnaireStep::whereHas('questionnaire', fn ($q) => $q->where('cooperation_id', $cooperation->id))
                ->where('step_id', $step->id)->max('order') ?? -1;

            $stepIds[$step->id] = ['order' => ++$orderForStep];
        }
        $questionnaire->steps()->attach($stepIds);

        return redirect()->route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'));
    }

    /**
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\Questionnaire $questionnaire
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $this->authorize('update', $questionnaire);

        $scans = Scan::with(['steps' => function ($query) {
            $query->whereNotIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater']);
        }])->get();

        return view('cooperation.admin.cooperation.questionnaires.questionnaire-editor', compact('questionnaire', 'scans'));
    }

    /**
     * Update the questionnaire and questions
     * if there are new questions create those too.
     *
     * @param \App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest $request
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\Questionnaire $questionnaire
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(QuestionnaireRequest $request, Cooperation $cooperation, Questionnaire $questionnaire): RedirectResponse
    {
        $this->authorize('update', $questionnaire);

        $data = $request->validated();
        $questionnaireData = $data['questionnaires'];
        $questionnaire->update(Arr::only($questionnaireData, 'name'));

        $steps = Step::findMany($questionnaireData['steps']);

        // Detach first to not weird out the order
        // TODO: Maybe we want to fix questionnaire_step order?
        $questionnaire->steps()->detach();

        $stepIds = [];
        foreach ($steps as $step) {
            $orderForStep = QuestionnaireStep::whereHas('questionnaire', fn ($q) => $q->where('cooperation_id', $cooperation->id))
                    ->where('step_id', $step->id)->max('order') ?? -1;

            $stepIds[$step->id] = ['order' => ++$orderForStep];
        }
        $questionnaire->steps()->attach($stepIds);

        // get the data for the questionnaire
        $validation = $data['validation'] ?? [];
        $order = 0;

        if ($request->has('questions')) {
            foreach ($request->input('questions') as $questionIdOrUuid => $questionData) {
                ++$order;
                QuestionnaireService::createOrUpdateQuestion($questionnaire, $questionIdOrUuid, $questionData, $validation, $order);
            }
        }

        return redirect()
            ->route('cooperation.admin.cooperation.questionnaires.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.questionnaires.edit.success'));
    }

    /**
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\Questionnaire $questionnaire
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        // TODO: Maybe we want to fix questionnaire_step order?
        $questionnaire->delete();

        return response(200);
    }

    /**
     * Detele a question (softdelete).
     *
     * @param \App\Models\Cooperation $cooperation
     * @param $questionId
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
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

        return response(202);
    }

    /**
     * Delete a question option.
     *
     * @param \App\Models\Cooperation $cooperation
     * @param $questionId
     * @param $questionOptionId
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
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

        return response(202);
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
