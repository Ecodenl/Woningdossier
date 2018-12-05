<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.index', compact('questionnaires'));
    }

    public function edit(Cooperation $cooperation, $questionnaireId)
    {
        $questionnaire = Questionnaire::find($questionnaireId);

        return view('cooperation.admin.cooperation.coordinator.questionnaires.questionnaire-editor', compact('questionnaire'));
    }

    public function create()
    {
        return view('cooperation.admin.cooperation.coordinator.questionnaires.create');
    }

    public function store(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');

        if ($request->has('questions.new')) {
            $newQuestions = $request->input('questions.new');

            // for now, later we will use the panel move drag drop shit to order.
            $order = 0;
            foreach ($newQuestions as $newQuestion) {
                $order++;
                $questionType = $newQuestion['type'];

                if ($questionType == 'text') {
                    $required = false;

                    if (array_key_exists('required', $newQuestion)) {
                        $required = true;
                    }

                    $uuid = Uuid::uuid4();

                    Question::create([
                        'name' => $uuid,
                        'type' => $questionType,
                        'order' => $order,
                        'required' => $required,
                        'questionnaire_id' => $questionnaireId
                    ]);

                    // multiple translations can be available
                    foreach ($newQuestion['question'] as $locale => $question) {
                        // the uuid we will put in the key for the translation and set in the question name column-
                        Translation::create([
                            'key' => $uuid,
                            'translation' => $question,
                            'language' => $locale
                        ]);
                    }
                } elseif ($questionType == 'select') {
                    $required = false;

                    if (array_key_exists('required', $newQuestion)) {
                        $required = true;
                    }

                    $uuid = Uuid::uuid4();

                    Question::create([
                        'name' => $uuid,
                        'type' => $questionType,
                        'order' => $order,
                        'required' => $required,
                        'questionnaire_id' => $questionnaireId
                    ]);

                    // multiple translations can be available
                    foreach ($newQuestion['question'] as $locale => $question) {
                        // the uuid we will put in the key for the translation and set in the question name column-
                        Translation::create([
                            'key' => $uuid,
                            'translation' => $question,
                            'language' => $locale
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Set a questionnaire active status
     *
     * @param Request $request
     */
    public function setActive(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');
        $active = $request->get('questionnaire_active');

        if ($active == "true") {
            $active = true;
        } else {
            $active = false;
        }

        $questionnaire = Questionnaire::find($questionnaireId);
        $questionnaire->is_active = $active;
        $questionnaire->save();

    }
}
