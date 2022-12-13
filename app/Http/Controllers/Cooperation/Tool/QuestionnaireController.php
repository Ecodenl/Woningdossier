<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\QuestionsAnswer;
use App\Models\Step;
use App\Services\Models\QuestionnaireService;
use App\Services\Scans\ScanFlowService;
use Illuminate\Http\RedirectResponse;

class QuestionnaireController extends Controller
{
    /**
     * Save or update the user his answers for the custom questionnaire.
     *
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Http\Requests\Cooperation\Tool\QuestionnaireRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cooperation $cooperation, QuestionnaireRequest $request): RedirectResponse
    {
        $building = HoomdossierSession::getBuilding(true);
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $questions = $request->validated()['questions'];

        $questionnaire = Questionnaire::find($request->input('questionnaire_id'));

        if (is_array($questions) && ! empty($questions)) {
            // the question answer can be a string or int.
            // it does not matter how we save it. Later when retrieving the answers we determine how we should show them based on the question type
            foreach ($questions as $questionId => $questionAnswer) {
                // this will only be a array if the user can select multiple answers for one question.
                // in the current state this will only be applied for a checkbox.

                // if its an array we will implode it, so we can explode it later on
                $answer = is_array($questionAnswer) ? implode('|', $questionAnswer) : $questionAnswer;

                // check if the answer is not empty
                if (! empty($answer)) {
                    QuestionsAnswer::updateOrCreate(
                        [
                            'question_id' => $questionId,
                            'building_id' => $building->id,
                            'input_source_id' => $currentInputSource->id,
                        ],
                        [
                            'answer' => $answer,
                        ]
                    );
                }
            }
        }

        QuestionnaireService::init()
            ->user($building->user)
            ->questionnaire($questionnaire)
            ->forInputSource($currentInputSource)
            ->completeQuestionnaire();

        $step = Step::findByShort($request->input('step_short'));

        return redirect()->to(
            ScanFlowService::init($step->scan, $building, $currentInputSource)
            ->forStep($step)
            ->forQuestionnaire($questionnaire)
            ->resolveNextUrl()
        );
    }
}
