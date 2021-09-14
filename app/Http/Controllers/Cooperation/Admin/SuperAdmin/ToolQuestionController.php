<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\ToolQuestionFormRequest;
use App\Models\Cooperation;
use App\Models\ToolQuestion;
use Illuminate\Http\Request;

class ToolQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $toolQuestions = ToolQuestion::all();

        return view('cooperation.admin.super-admin.tool-questions.index', compact('toolQuestions'));
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cooperation $cooperation, ToolQuestion $toolQuestion)
    {
        return view('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ToolQuestionFormRequest $request, Cooperation $cooperation, ToolQuestion $toolQuestion)
    {
        $toolQuestion->update($request->validated()['tool_questions']);

        return redirect()
            ->route('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'))
            ->with('success', __('cooperation/admin/super-admin/tool-questions.update.success'));
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
