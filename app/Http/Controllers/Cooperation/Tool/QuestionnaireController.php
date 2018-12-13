<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Tool\QuestionnaireRequest;
use App\Models\QuestionsAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionnaireController extends Controller
{
    /**
     * Save or update the user his answers for the custom questionnaire
     *
     * @param QuestionnaireRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(QuestionnaireRequest $request)
    {
        $questions = $request->get('questions');

        // the question answer can be a string or int.
        // it does not matter how we save it. Later when retrieving the answers we determine how we should show them based on the question type
        foreach ($questions as $questionId => $questionAnswer) {

            // this will only be a array if the input type is a checkbox, because there can be more answers selected.
            if (is_array($questionAnswer)) {
                foreach ($questionAnswer as $answer) {
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
            } else {
                QuestionsAnswer::updateOrCreate(
                    [
                        'question_id' => $questionId,
                        'building_id' => HoomdossierSession::getBuilding(),
                        'input_source_id' => HoomdossierSession::getInputSource(),
                    ],
                    [
                        'answer' => $questionAnswer
                    ]
                );
            }
        }

        // something that should be discussed, we could redirect them to the next step with the stephelper
        return redirect()->back();
    }
}
