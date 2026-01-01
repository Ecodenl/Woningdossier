<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\Step;

class QuestionnaireController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan, Step $step, Questionnaire $questionnaire): View|RedirectResponse
    {
        // Ensure it's a valid questionnaire
        if ($questionnaire->isNotActive() || $questionnaire->steps()->where('steps.id', $step->id)->doesntExist()) {
            // Before, we would abort with a 404. This made sense if a user was actively trying to find an inactive
            // questionnaire. However, it is entirely possible for a user to have a questionnaire as last
            // opened URL. In that case, if it becomes inactive, then suddenly the user has a 404 in their face
            // and no simple way to return to the tool. Instead, we will redirect them to the action plan, which
            // will then redirect them to an available step if they cannot access the woonplan.
            return to_route(
                'cooperation.frontend.tool.simple-scan.my-plan.index',
                compact('cooperation', 'scan')
            );
        }

        return view("cooperation.frontend.tool.simple-scan.questionnaires.index", compact('scan', 'step', 'questionnaire'));
    }
}
