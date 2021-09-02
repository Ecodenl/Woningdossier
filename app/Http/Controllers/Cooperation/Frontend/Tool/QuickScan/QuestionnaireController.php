<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\QuickScan;

use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index(Cooperation $cooperation, Step $step, Questionnaire $questionnaire)
    {
        // Ensure it's a valid questionnaire
        abort_if($questionnaire->isNotActive() || $questionnaire->step->id !== $step->id, 404);

        return view('cooperation.frontend.tool.quick-scan.questionnaires.index', compact('step', 'questionnaire'));
    }
}
