<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Enums\MappingType;
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
    public function index(Cooperation $cooperation): View
    {
        $measureCategories = MeasureCategory::all();

        return view('cooperation.admin.super-admin.measure-categories.index', compact('measureCategories'));
    }
    
    public function create(Cooperation $cooperation): View
    {
        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']) ?? [];

        return view('cooperation.admin.super-admin.measure-categories.create', compact('measures'));
    }

    public function store(MeasureCategoryRequest $request, Cooperation $cooperation, MappingService $mappingService): RedirectResponse
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
                $mappingService->from($measureCategory)->sync([$targetData], MappingType::MEASURE_CATEGORY_VBJEHUIS->value);
            }
        }

        return to_route('cooperation.admin.super-admin.measure-categories.index')
            ->with('success', __('cooperation/admin/super-admin/measure-categories.store.success'));
    }

    public function edit(Cooperation $cooperation, MeasureCategory $measureCategory, MappingService $mappingService): View
    {
        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']) ?? [];
        $currentMapping = $mappingService
            //->type(MappingType::MEASURE_CATEGORY_VBJEHUIS->value)
            ->from($measureCategory)
            ->resolveMapping()
            ->first();

        return view('cooperation.admin.super-admin.measure-categories.edit', compact('measureCategory', 'measures', 'currentMapping'));
    }

    public function update(MeasureCategoryRequest $request, Cooperation $cooperation, MeasureCategory $measureCategory, MappingService $mappingService): RedirectResponse
    {
        $data = $request->validated();
        $categoryData = $data['measure_categories'];
        $measure = $data['vbjehuis_measure'] ?? null;

        $measureCategory->update($categoryData);

        $measures = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Measures']);
        if (! empty($measures)) {
            $mappingService
                //->type(MappingType::MEASURE_CATEGORY_VBJEHUIS->value)
                ->from($measureCategory);

            if (! empty($measure)) {
                // If not empty, then the request has validated it and we know it's available.
                $targetData = Arr::first(Arr::where($measures, fn ($a) => $a['Value'] == $measure));
                $mappingService->sync([$targetData], MappingType::MEASURE_CATEGORY_VBJEHUIS->value);
            } else {
                $mappingService->detach();
            }
        }

        return to_route('cooperation.admin.super-admin.measure-categories.index')
            ->with('success', __('cooperation/admin/super-admin/measure-categories.update.success'));
    }
}
