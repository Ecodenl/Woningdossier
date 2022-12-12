<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\ExpertScan;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;

class QuestionnaireController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan, Step $step, Questionnaire $questionnaire)
    {
        // Ensure it's a valid questionnaire
        abort_if($questionnaire->isNotActive() || $questionnaire->steps()->where('steps.id', $step->id)->doesntExist(), 404);

        return view("cooperation.frontend.tool.expert-scan.questionnaires.index", compact('scan', 'step', 'questionnaire'));
    }
}
