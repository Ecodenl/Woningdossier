<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\LanguageLine;
use App\Models\Step;
use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = Step::all();

        return view('cooperation.admin.super-admin.translations.index', compact('steps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * @param Cooperation $cooperation
     * @param string      $stepSlug|   So we can get the translations / questions from language_line table for the step
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Cooperation $cooperation, $stepSlug)
    {
        $step = Step::where('slug', $stepSlug)->first();
        if ($step instanceof Step) {
            $questions = LanguageLine::where('step_id', $step->id)
                ->mainQuestions()
                ->get();

        // if it isn't a instance, then its a general translation group
        } elseif (in_array($stepSlug, ['general', 'my-plan', 'home'])) {
            $questions = LanguageLine::where('group', $stepSlug)
                ->mainQuestions()
                ->get();
        }

        return view('cooperation.admin.super-admin.translations.edit', compact('questions', 'stepSlug'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Cooperation              $cooperation
     * @param string                   $stepId
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cooperation $cooperation, $stepSlug)
    {
        $languageLinesData = $request->get('language_lines', []);

        foreach ($languageLinesData as $locale => $languageLineData) {
            foreach ($languageLineData as $type => $languageLines) {
                // we dont do stuff with the type yet, could be helpfull in the future.
                foreach ($languageLines as $languageLineId => $text) {
                    $text = $text ?? '';
                    $languageLine = LanguageLine::find($languageLineId);
                    if ($languageLine instanceof LanguageLine) {
                        $languageLine->setTranslation($locale, $text);
                        $languageLine->save();
                    }
                }
            }
        }

        return redirect(route('cooperation.admin.super-admin.translations.edit', ['step-slug' => $stepSlug]))
            ->with('success', __('woningdossier.cooperation.admin.super-admin.translations.update.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
