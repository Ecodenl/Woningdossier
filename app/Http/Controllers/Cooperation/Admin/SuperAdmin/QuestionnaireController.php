<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Jobs\CopyQuestionnaireToCooperation;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index(): View
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.super-admin.questionnaires.index', compact('questionnaires'));
    }

    public function show(Cooperation $cooperation, Questionnaire $questionnaire): View
    {
        $questionnaires = Questionnaire::all();
        $cooperations = Cooperation::all();
        $selectedQuestionnaire = $questionnaire;

        return view('cooperation.admin.super-admin.questionnaires.edit', compact('selectedQuestionnaire', 'questionnaires', 'cooperations'));
    }

    /**
     * Copy the questionnaire to other cooperations.
     */
    public function copy(Request $request): RedirectResponse
    {
        $questionnaireId = $request->input('questionnaires.id');
        $cooperationIds = $request->input('cooperations.id', []);

        // the questionnaire we will copy to the given cooperations
        $questionnaire = Questionnaire::findOrFail($questionnaireId);

        foreach ($cooperationIds as $cooperationId) {
            $cooperation = Cooperation::find($cooperationId);
            CopyQuestionnaireToCooperation::dispatch($cooperation, $questionnaire);
        }

        return to_route('cooperation.admin.super-admin.questionnaires.index')
            ->with('success', __('cooperation/admin/super-admin/questionnaires.copy.success'));
    }
}
