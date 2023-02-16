<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Events\CooperationMeasureApplicationUpdated;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationFormRequest;
use App\Jobs\HandleCooperationMeasureApplicationDeletion;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Arr;

class CooperationMeasureApplicationController extends Controller
{
    public function index(Cooperation $cooperation, string $type)
    {
        $scope = "{$type}Measures";

        $cooperationMeasureApplications = $cooperation->cooperationMeasureApplications()->{$scope}()->get();

        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('cooperationMeasureApplications', 'type'));
    }

    public function create(Cooperation $cooperation, string $type)
    {
        $measures = RegulationService::init()->getFilters()['Measures'];
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.create', compact('type', 'measures'));
    }

    public function store(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation, string $type)
    {
        $measureData = $request->validated()['cooperation_measure_applications'];
        $measureCategory = $measureData['measure_category'] ?? null;
        unset($measureData['measure_category']);
        $measureData['cooperation_id'] = $cooperation->id;
        $measureData['is_extensive_measure'] = $type === CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE;
        $measureData['is_deletable'] = true;

        foreach ($measureData['name'] as $locale => $content) {
            $measureData['name'][$locale] = strip_tags($content);
        }
        $cooperationMeasureApplication = CooperationMeasureApplication::create($measureData);

        if (! is_null($measureCategory)) {
            $targetData = Arr::first(Arr::where(RegulationService::init()->getFilters()['Measures'], fn ($a) => $a['Value'] === $measureCategory));
            MappingService::init()->from($cooperationMeasureApplication)->sync([$targetData]);
        }

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('type'))
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.store.success'));
    }

    public function edit(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $type = $cooperationMeasureApplication->getType();
        $measures = RegulationService::init()->getFilters()['Measures'];
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit', compact('cooperationMeasureApplication', 'type', 'measures'));
    }

    public function update(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $measureData = $request->validated()['cooperation_measure_applications'];
        $measureCategory = $measureData['measure_category'] ?? null;
        unset($measureData['measure_category']);
        foreach ($measureData['name'] as $locale => $content) {
            $measureData['name'][$locale] = strip_tags($content);
        }

        $cooperationMeasureApplication->update($measureData);

        if (! is_null($measureCategory)) {
            $targetData = Arr::first(Arr::where(RegulationService::init()->getFilters()['Measures'], fn ($a) => $a['Value'] === $measureCategory));
            MappingService::init()->from($cooperationMeasureApplication)->sync([$targetData]);
        }
        CooperationMeasureApplicationUpdated::dispatch($cooperationMeasureApplication);

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => $cooperationMeasureApplication->getType()])
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.update.success'));
    }

    public function destroy(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $this->authorize('delete', $cooperationMeasureApplication);

        // first we soft delete it, this makes it! impossible for users to add it.
        $cooperationMeasureApplication->delete();

        HandleCooperationMeasureApplicationDeletion::dispatch($cooperationMeasureApplication);

        return redirect()->route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => $cooperationMeasureApplication->getType()])
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.success'));
    }
}
