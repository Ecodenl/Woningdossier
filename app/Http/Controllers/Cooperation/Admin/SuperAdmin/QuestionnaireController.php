<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Jobs\CopyQuestionnaireToCooperation;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.super-admin.questionnaires.index', compact('questionnaires'));
    }

    public function show(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $questionnaires = Questionnaire::all();
        $cooperations = Cooperation::all();
        $selectedQuestionnaire = $questionnaire;

        return view('cooperation.admin.super-admin.questionnaires.edit', compact('selectedQuestionnaire', 'questionnaires', 'cooperations'));
    }

    /**
     * Copy the questionnaire to other cooperations.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Request $request)
    {
        $questionnaireId = $request->input('questionnaires.id');
        $cooperationIds = $request->input('cooperations.id', []);

        // the questionnaire we will copy to the given cooperations
        $questionnaire = Questionnaire::find($questionnaireId);

        foreach ($cooperationIds as $cooperationId) {
            $cooperation = Cooperation::find($cooperationId);
            CopyQuestionnaireToCooperation::dispatch($cooperation, $questionnaire);
        }

        return redirect()
            ->route('cooperation.admin.super-admin.questionnaires.index')
            ->with('success', __('cooperation/admin/super-admin/questionnaires.copy.success'));
    }
}
