<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationFormRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
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
        // We can't delete the cooperation measure application straight away. We need to check
        // if it's used in any UserActionPlanAdvices.
        $advices = $cooperationMeasureApplication->userActionPlanAdvices()->allInputSources()->get();
        $processedUserIds = [];

        // We need the master
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        foreach ($advices as $advice) {
            // The master input source makes this a massive pain
            // First we check if we haven't already processed this set
            // We need a valid building for this to work
            $user = $advice->user;
            if (! in_array($user->id, $processedUserIds) && $user->building instanceof Building) {
                // Get all advices for this user id
                $advicesForUserId = $advices->where('user_id', $user->id)->get();
                $inputSourceIds = $advicesForUserId->where('input_source_id', '!=', $masterInputSource->id)->pluck('input_source_id');

                $hash = Str::uuid();
                $createData = [
                    'building_id' => $user->building->id,
                    'name' => $cooperationMeasureApplication->name,
                    'info' => $cooperationMeasureApplication->info,
                    'hash' => $hash,
                ];
                foreach ($inputSourceIds as $inputSourceId) {
                    $createData['input_source_id'] = $inputSourceId;

                    CustomMeasureApplication::create($createData);
                }

                $processedUserIds[] = $user->id;
            }
        }


//        $cooperationMeasureApplication->delete();

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.success'));
    }
}
