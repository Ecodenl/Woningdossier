<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Illuminate\Http\Response;
use App\Helpers\Arr;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\QuestionnaireStep;
use App\Models\Scan;
use App\Models\Step;
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
        Gate::authorize('create', Questionnaire::class);

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
        Gate::authorize('create', Questionnaire::class);

        $questionnaireData = $request->validated()['questionnaires'];

        $questionnaire = Questionnaire::create([
            'name' => $questionnaireData['name'],
            'cooperation_id' => $cooperation->id,
            'is_active' => false,
        ]);

        $steps = Step::findMany($questionnaireData['steps']);

        $this->attachStepIds($steps, $cooperation, $questionnaire);

        return to_route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'));
    }

    public function edit(Cooperation $cooperation, Questionnaire $questionnaire): View
    {
        Gate::authorize('update', $questionnaire);

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
        Gate::authorize('update', $questionnaire);

        $data = $request->validated();
        $questionnaireData = $data['questionnaires'];
        $questionnaire->update(Arr::only($questionnaireData, 'name'));

        $steps = Step::findMany($questionnaireData['steps']);

        // Detach first to not weird out the order
        // TODO: Maybe we want to fix questionnaire_step order?
        $questionnaire->steps()->detach();

        $this->attachStepIds($steps, $cooperation, $questionnaire);

        return to_route('cooperation.admin.cooperation.questionnaires.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.success'));
    }

    public function destroy(Cooperation $cooperation, Questionnaire $questionnaire): Response
    {
        // TODO: Maybe we want to fix questionnaire_step order?
        $questionnaire->delete();

        return response(null, 200);
    }

    /**
     * Set the active status from a questionnaire.
     */
    public function setActive(Request $request): int
    {
        $questionnaireId = $request->input('questionnaire_id');
        $active = $request->input('questionnaire_active');
        $questionnaire = Questionnaire::find($questionnaireId);

        Gate::authorize('setActiveStatus', $questionnaire);

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
