<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\MappingHelper;
use App\Helpers\Wrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\MeasureCategoryRequest;
use App\Models\Cooperation;
use App\Models\MeasureCategory;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Arr;

class MeasureCategoryController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $measureCategories = MeasureCategory::all();

        return view('cooperation.admin.super-admin.measure-categories.index', compact('measureCategories'));
    }
    
    public function create(Cooperation $cooperation)
    {
        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']) ?? [];

        return view('cooperation.admin.super-admin.measure-categories.create', compact('measures'));
    }

    public function store(MeasureCategoryRequest $request, Cooperation $cooperation, MappingService $mappingService)
    {
        $data = $request->validated();
        $categoryData = $data['measure_categories'];
        $measure = $data['vbjehuis_measure'] ?? null;

        $measureCategory = MeasureCategory::create($categoryData);

        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']);
        if (! empty($measures)) {
            if (! empty($measure)) {
                // If not empty, then the request has validated it and we know it's available.
                $targetData = Arr::first(Arr::where($measures, fn ($a) => $a['Value'] == $measure));
                $mappingService->from($measureCategory)->sync([$targetData]);
            }
        }

        return redirect()->route('cooperation.admin.super-admin.measure-categories.index')
            ->with('success', __('cooperation/admin/super-admin/measure-categories.store.success'));
    }

    public function edit(Cooperation $cooperation, MeasureCategory $measureCategory)
    {
        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']) ?? [];
        $currentMapping = MappingService::init()
            ->from($measureCategory)
            ->resolveMapping()
            ->first();

        return view('cooperation.admin.super-admin.measure-categories.edit', compact('measureCategory', 'measures', 'currentMapping'));
    }

    public function update(MeasureCategoryRequest $request, Cooperation $cooperation, MeasureCategory $measureCategory, MappingService $mappingService)
    {
        $data = $request->validated();
        $categoryData = $data['measure_categories'];
        $measure = $data['vbjehuis_measure'] ?? null;

        $measureCategory->update($categoryData);

        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']);
        if (! empty($measures)) {
            $mappingService->from($measureCategory);

            if (! empty($measure)) {
                // If not empty, then the request has validated it and we know it's available.
                $targetData = Arr::first(Arr::where($measures, fn ($a) => $a['Value'] == $measure));
                $mappingService->sync([$targetData]);
            } else {
                $mappingService->detach();
            }
        }

        return redirect()->route('cooperation.admin.super-admin.measure-categories.index')
            ->with('success', __('cooperation/admin/super-admin/measure-categories.update.success'));
    }
}
