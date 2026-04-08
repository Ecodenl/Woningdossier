<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Events\CooperationMeasureApplicationUpdated;
use App\Enums\MappingType;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\CooperationMeasureApplicationFormRequest;
use App\Jobs\HandleCooperationMeasureApplicationDeletion;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\Mapping;
use App\Models\MeasureCategory;
use App\Services\MappingService;

class CooperationMeasureApplicationController extends Controller
{
    public function index(Cooperation $cooperation, string $type): View
    {
        $scope = "{$type}Measures";

        $cooperationMeasureApplications = $cooperation->cooperationMeasureApplications()->{$scope}()->get();

        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('cooperationMeasureApplications', 'type'));
    }

    public function create(Cooperation $cooperation, string $type): View
    {
        $measures = MeasureCategory::all();
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.create', compact('type', 'measures'));
    }

    public function store(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation, string $type, MappingService $mappingService): RedirectResponse
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

        $measureCategory = MeasureCategory::find($measureCategory);
        if ($measureCategory instanceof MeasureCategory) {
            $mappingService->from($cooperationMeasureApplication)
                ->sync([$measureCategory], MappingType::COOPERATION_MEASURE_APPLICATION_MEASURE_CATEGORY->value);
        }

        CooperationMeasureApplicationUpdated::dispatch($cooperationMeasureApplication);

        return to_route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', compact('type'))
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.store.success'));
    }

    public function edit(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication, MappingService $mappingService): View
    {
        $type = $cooperationMeasureApplication->getType();
        $measures = MeasureCategory::all();
        $currentMeasure = null;
        $mapping = $mappingService->from($cooperationMeasureApplication)
            ->resolveMapping()
            ->first();
        if ($mapping instanceof Mapping) {
            $currentMeasure = $mapping->mappable?->id;
        }
        return view('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit', compact('cooperationMeasureApplication', 'type', 'measures', 'currentMeasure'));
    }

    public function update(CooperationMeasureApplicationFormRequest $request, Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication, MappingService $mappingService): RedirectResponse
    {
        $measureData = $request->validated()['cooperation_measure_applications'];
        $measureCategory = $measureData['measure_category'] ?? null;
        unset($measureData['measure_category']);
        foreach ($measureData['name'] as $locale => $content) {
            $measureData['name'][$locale] = strip_tags($content);
        }

        $cooperationMeasureApplication->update($measureData);

        $measureCategory = MeasureCategory::find($measureCategory);
        $mappingService
            ->from($cooperationMeasureApplication);
        $measureCategory instanceof MeasureCategory ? $mappingService->sync([$measureCategory], MappingType::COOPERATION_MEASURE_APPLICATION_MEASURE_CATEGORY->value) : $mappingService->detach();

        CooperationMeasureApplicationUpdated::dispatch($cooperationMeasureApplication);

        return to_route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => $cooperationMeasureApplication->getType()])
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.update.success'));
    }

    public function destroy(Cooperation $cooperation, CooperationMeasureApplication $cooperationMeasureApplication): RedirectResponse
    {
        Gate::authorize('delete', $cooperationMeasureApplication);

        // First we soft delete it, this makes it impossible for users to add it.
        $cooperationMeasureApplication->delete();

        HandleCooperationMeasureApplicationDeletion::dispatch($cooperationMeasureApplication);

        return to_route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => $cooperationMeasureApplication->getType()])
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.success'));
    }
}
