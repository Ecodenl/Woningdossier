<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return view('cooperation.admin.super-admin.questionnaires.edit', compact('questionnaires', 'cooperations'));
    }


    /**
     * Copy the questionnaire to other cooperations
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Request $request)
    {
        $questionnaireId = $request->input('questionnaires.id');
        $cooperationIds = $request->input('cooperations.id');

        // the questionnaire we will copy to the given cooperations
        $questionnaire = Questionnaire::find($questionnaireId);

        foreach ($cooperationIds as $cooperationId) {
            /** @var Questionnaire $questionnaireToReplicate */
            $questionnaireToReplicate = $questionnaire->replicate();

            // for now this will do it as there is only one translation which is dutch.
            // we MUST create a new translation because this will generate a new record in the translations table
            // this way each cooperation can edit the question names without messing up the other cooperation its questionnaires.
            $questionnaireToReplicate->cooperation_id = $cooperationId;
            $questionnaireToReplicate->createTranslations('name', ['nl' => $questionnaire->name]);
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
        return redirect()
            ->route('cooperation.admin.super-admin.questionnaires.index')
            ->with('success', __('cooperation/admin/super-admin/questionnaires.copy.success'));
    }
}
