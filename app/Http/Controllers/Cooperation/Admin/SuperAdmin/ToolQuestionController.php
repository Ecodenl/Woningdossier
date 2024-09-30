<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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
    public function index(): View
    {
        $toolQuestions = ToolQuestion::all();

        return view('cooperation.admin.super-admin.tool-questions.index', compact('toolQuestions'));
    }

    /**
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Cooperation $cooperation, ToolQuestion $toolQuestion): View
    {
        return view('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'));
    }

    public function update(ToolQuestionFormRequest $request, Cooperation $cooperation, ToolQuestion $toolQuestion): RedirectResponse
    {
        $toolQuestion->update($request->validated()['tool_questions']);

        return redirect()
            ->route('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'))
            ->with('success', __('cooperation/admin/super-admin/tool-questions.update.success'));
    }
}
