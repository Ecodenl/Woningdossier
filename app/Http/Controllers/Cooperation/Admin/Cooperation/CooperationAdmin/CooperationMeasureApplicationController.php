<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\Step;
use Illuminate\Http\Request;

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

    public function store(Cooperation $cooperation)
    {

    }
    public function edit(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit');
    }

    public function update(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {

    }

    public function destroy(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $cooperationMeasureApplication->delete();

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.success'));
    }
}
