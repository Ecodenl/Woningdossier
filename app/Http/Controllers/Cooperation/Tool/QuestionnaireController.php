<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Requests\Cooperation\Tool\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\QuestionsAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionnaireController extends Controller
{
    /**
     * Save or update the user his answers for the custom questionnaire
     *
     * @param Cooperation $cooperation
     * @param QuestionnaireRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cooperation $cooperation, QuestionnaireRequest $request)
    {
        $questions = $request->get('questions');

        $questionnaireId = $request->get('questionnaire_id');
        $questionnaire = Questionnaire::find($questionnaireId);

        // the question answer can be a string or int.
        // it does not matter how we save it. Later when retrieving the answers we determine how we should show them based on the question type
        foreach ($questions as $questionId => $questionAnswer) {

            // this will only be a array if the user can select multiple answers for one question.
            // in the current state this will only be applied for a checkbox.
            if (is_array($questionAnswer)) {
                $answer = "";

                // we pipe the answer, later on we can explode it and check it against the question ids
                foreach ($questionAnswer as $qAnswer) {
                    $answer .= "{$qAnswer}|";
                }

            } else {
                $answer = $questionAnswer;
            }


            // check if the answer is not empty
            if (!empty($answer)) {
                QuestionsAnswer::updateOrCreate(
                    [
                        'question_id' => $questionId,
                        'building_id' => HoomdossierSession::getBuilding(),
                        'input_source_id' => HoomdossierSession::getInputSource(),
                    ],
                    [
                        'answer' => $answer
                    ]
                );
            }
        }

        \Auth::user()->completeQuestionnaire($questionnaire);

        // something that should be discussed, we could redirect them to the next step with the stephelper
        return redirect()->back();
//        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
    }
}
