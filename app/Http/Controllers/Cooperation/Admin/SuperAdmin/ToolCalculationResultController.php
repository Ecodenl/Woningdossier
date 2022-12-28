<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

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
    public function index()
    {
        $toolCalculationResults = ToolCalculationResult::where('short', 'LIKE', '%heat-pump%')
            ->orWhere('short', 'LIKE', '%sun-boiler%')
            ->orWhere('short', 'LIKE', '%hr-boiler%')
            ->get();

        return view('cooperation.admin.super-admin.tool-calculation-results.index', compact('toolCalculationResults'));
    }

    /**
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\ToolCalculationResult $toolQuestion
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Cooperation $cooperation, ToolCalculationResult $toolCalculationResult)
    {
        return view('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'));
    }

    /**
     * @param \App\Http\Requests\Cooperation\Admin\SuperAdmin\ToolCalculationResultFormRequest $request
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\ToolCalculationResult $toolQuestion
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ToolCalculationResultFormRequest $request, Cooperation $cooperation, ToolCalculationResult $toolCalculationResult)
    {
        $toolCalculationResult->update($request->validated()['tool_calculation_results']);

        return redirect()
            ->route('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'))
            ->with('success', __('cooperation/admin/super-admin/tool-calculation-results.update.success'));
    }
}
