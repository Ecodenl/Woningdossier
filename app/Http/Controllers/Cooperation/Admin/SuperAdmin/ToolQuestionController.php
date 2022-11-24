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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $toolQuestions = ToolQuestion::all();

        return view('cooperation.admin.super-admin.tool-questions.index', compact('toolQuestions'));
    }

    /**
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\ToolQuestion $toolQuestion
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Cooperation $cooperation, ToolQuestion $toolQuestion)
    {
        return view('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'));
    }

    /**
     * @param \App\Http\Requests\Cooperation\Admin\SuperAdmin\ToolQuestionFormRequest $request
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\ToolQuestion $toolQuestion
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ToolQuestionFormRequest $request, Cooperation $cooperation, ToolQuestion $toolQuestion)
    {
        $toolQuestion->update($request->validated()['tool_questions']);

        return redirect()
            ->route('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'))
            ->with('success', __('cooperation/admin/super-admin/tool-questions.update.success'));
    }
}
