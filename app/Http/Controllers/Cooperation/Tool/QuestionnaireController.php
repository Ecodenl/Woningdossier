<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Tool\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\QuestionsAnswer;
use App\Services\Models\QuestionnaireService;
use App\Models\Scan;
use App\Services\Scans\ScanFlowService;

class QuestionnaireController extends Controller
{
    /**
     * Save or update the user his answers for the custom questionnaire.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cooperation $cooperation, QuestionnaireRequest $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        $questions = $request->input('questions');

        $questionnaireId = $request->get('questionnaire_id');
        $questionnaire = Questionnaire::find($questionnaireId);

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
                            'building_id' => HoomdossierSession::getBuilding(),
                            'input_source_id' => HoomdossierSession::getInputSource(),
                        ],
                        [
                            'answer' => $answer,
                        ]
                    );
                }
            }
        }

        $currentInputSource = HoomdossierSession::getInputSource(true);

        QuestionnaireService::init()
            ->user($building->user)
            ->questionnaire($questionnaire)
            ->forInputSource($currentInputSource)
            ->completeQuestionnaire();

        $step = $questionnaire->step;
        $quickScan = Scan::bySlug('quick-scan')->first();

        // TODO: For now only use scan flow service for quick scan
        if ($step->scan_id === $quickScan->id) {
            return redirect()->to(ScanFlowService::init($quickScan, $building, $currentInputSource)
                ->forStep($step)
                ->forQuestionnaire($questionnaire)
                ->resolveNextUrl());
        }

        $url = StepHelper::getNextExpertStep($questionnaire->step, $questionnaire);

        return redirect($url);
    }
}
