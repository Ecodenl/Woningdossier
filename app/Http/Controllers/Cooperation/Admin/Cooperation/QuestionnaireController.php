<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Illuminate\Http\Response;
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
    public function index(): View
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.questionnaires.index', compact('questionnaires'));
    }

    public function create(): View
    {
        $this->authorize('create', Questionnaire::class);

        $scans = Scan::with(['steps' => function ($query) {
            $query->whereNotIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater']);
        }])->get();

        return view('cooperation.admin.cooperation.questionnaires.create', compact('scans'));
    }

    /**
     * Store a questionnaire, after this the user will get redirected to the edit page and he can add questions to the questionnaire.
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

        $this->attachStepIds($steps, $cooperation, $questionnaire);

        return redirect()->route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'));
    }

    public function edit(Cooperation $cooperation, Questionnaire $questionnaire): View
    {
        $this->authorize('update', $questionnaire);

        $scans = Scan::with(['steps' => function ($query) {
            $query->whereNotIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater']);
        }])->get();

        return view('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire', 'scans'));
    }

    /**
     * Update the questionnaire and questions
     * if there are new questions create those too.
     *
     *
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

        $this->attachStepIds($steps, $cooperation, $questionnaire);

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
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.success'));
    }

    public function destroy(Cooperation $cooperation, Questionnaire $questionnaire): Response
    {
        // TODO: Maybe we want to fix questionnaire_step order?
        $questionnaire->delete();

        return response(200);
    }

    /**
     * Detele a question (softdelete).
     */
    public function deleteQuestion(Cooperation $cooperation, $questionId): Response
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
     */
    public function deleteQuestionOption(Cooperation $cooperation, $questionId, $questionOptionId): Response
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
     */
    public function setActive(Request $request): int
    {
        $questionnaireId = $request->input('questionnaire_id');
        $active = $request->input('questionnaire_active');
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('setActiveStatus', $questionnaire);

        $active = 'true' == $active;

        $questionnaire->update(['is_active' => $active]);

        return $questionnaireId;
    }

    public function attachStepIds(Collection $steps, Cooperation $cooperation, Questionnaire $questionnaire): void
    {
        $stepIds = [];
        foreach ($steps as $step) {
            $orderForStep = QuestionnaireStep::whereHas(
                'questionnaire',
                fn ($q) => $q->where('cooperation_id', $cooperation->id)
            )->where('step_id', $step->id)->max('order') ?? -1;

            $stepIds[$step->id] = ['order' => ++$orderForStep];
        }
        $questionnaire->steps()->attach($stepIds);
    }
}
