<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\MeasureApplicationFormRequest;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use Illuminate\Http\Request;

class MeasureApplicationController extends Controller
{
    public function index(): View
    {
        $measureApplications = MeasureApplication::all();

        return view('cooperation.admin.super-admin.measure-applications.index', compact('measureApplications'));
    }
    
    public function edit(Cooperation $cooperation, MeasureApplication $measureApplication): View
    {
        return view('cooperation.admin.super-admin.measure-applications.edit', compact('measureApplication'));
    }
    
    public function update(MeasureApplicationFormRequest $request, Cooperation $cooperation, MeasureApplication $measureApplication): RedirectResponse
    {
        $measureApplicationData = $request->validated()['measure_applications'];

        // Merge, comfort cannot currently be set by users, but we don't want to override it.
        $measureApplicationData['configurations'] = array_merge($measureApplication->configurations, $measureApplicationData['configurations']);

        $measureApplication->update($measureApplicationData);

        return redirect()
            ->route('cooperation.admin.super-admin.measure-applications.index')
            ->with('success', __('cooperation/admin/super-admin/measure-applications.update.success'));
    }
}
