<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Models\Cooperation;
use App\Models\LanguageLine;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @param Cooperation $cooperation
     * @param int $stepId | So we can get the translations / questions from language_line table for the step.
     *
     * @return \Response
     */
    public function show(Cooperation $cooperation, $stepId)
    {
        $questions = LanguageLine::with(['subQuestions', 'helpTexts'])
            ->where('step_id', $stepId)
            ->mainQuestions()
            ->get();

        return view('cooperation.admin.super-admin.translations.show', compact('questions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
