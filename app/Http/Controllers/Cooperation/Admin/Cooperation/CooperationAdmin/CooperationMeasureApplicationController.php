<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Helpers\Queue;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationFormRequest;
use App\Jobs\MoveCooperationMeasureApplicationToCustomMeasureApplications;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CooperationMeasureApplicationController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $cooperationMeasureApplications = $cooperation->cooperationMeasureApplications;

        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('cooperationMeasureApplications'));
    }

    public function create(Cooperation $cooperation)
    {
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.create');
    }

    public function store(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation)
    {
        $measureData = $request->validated()['cooperation_measure_applications'];
        $measureData['cooperation_id'] = $cooperation->id;

        CooperationMeasureApplication::create($measureData);

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.store.success'));
    }

    public function edit(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit', compact('cooperationMeasureApplication'));
    }

    public function update(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $measureData = $request->validated()['cooperation_measure_applications'];

        $cooperationMeasureApplication->update($measureData);

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.update.success'));
    }

    public function destroy(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        // first we soft delete it, this makes it impossible for users to add it.
        $cooperationMeasureApplication->delete();

        MoveCooperationMeasureApplicationToCustomMeasureApplications::dispatch($cooperationMeasureApplication);

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.success'));
    }
}
