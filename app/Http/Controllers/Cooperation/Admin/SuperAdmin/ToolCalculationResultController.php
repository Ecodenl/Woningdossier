<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\ToolCalculationResultFormRequest;
use App\Models\Cooperation;
use App\Models\ToolCalculationResult;
use Illuminate\Http\Request;

class ToolCalculationResultController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $toolCalculationResults = ToolCalculationResult::where('short', 'LIKE', '%heat-pump%')
            ->orWhere('short', 'LIKE', '%sun-boiler%')
            ->orWhere('short', 'LIKE', '%hr-boiler%')
            ->get();

        return view('cooperation.admin.super-admin.tool-calculation-results.index', compact('toolCalculationResults'));
    }

    /**
     * @param \App\Models\ToolCalculationResult $toolQuestion
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Cooperation $cooperation, ToolCalculationResult $toolCalculationResult): View
    {
        return view('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'));
    }

    /**
     * @param \App\Models\ToolCalculationResult $toolQuestion
     */
    public function update(ToolCalculationResultFormRequest $request, Cooperation $cooperation, ToolCalculationResult $toolCalculationResult): RedirectResponse
    {
        $toolCalculationResult->update($request->validated()['tool_calculation_results']);

        return redirect()
            ->route('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'))
            ->with('success', __('cooperation/admin/super-admin/tool-calculation-results.update.success'));
    }
}
