<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\MappingHelper;
use App\Helpers\Wrapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\MunicipalityCoupleRequest;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\MunicipalityRequest;
use App\Models\Cooperation;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Models\MunicipalityService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Arr;

class MunicipalityController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $municipalities = Municipality::all();

        return view('cooperation.admin.super-admin.municipalities.index', compact('municipalities'));
    }
    
    public function create(Cooperation $cooperation)
    {
        return view('cooperation.admin.super-admin.municipalities.create');
    }

    public function store(MunicipalityRequest $request, Cooperation $cooperation)
    {
        $data = $request->validated()['municipalities'];
        $municipality = Municipality::create($data);

        return redirect()->route('cooperation.admin.super-admin.municipalities.show', compact('municipality'))
            ->with('success', __('cooperation/admin/super-admin/municipalities.store.success'));
    }

    public function show(Cooperation $cooperation, Municipality $municipality, MunicipalityService $municipalityService)
    {
        $municipalityService->forMunicipality($municipality);
        $bagMunicipalities = $municipalityService->getAvailableBagMunicipalities();
        $mappedVbjehuisMunicipality = $municipalityService->retrieveVbjehuisMuncipality();
        // TODO: Wrapper::wrapCall
        $vbjehuisMunicipalities = RegulationService::init()->getFilters()['Cities'];

        return view(
            'cooperation.admin.super-admin.municipalities.show',
            compact('municipality', 'bagMunicipalities', 'mappedVbjehuisMunicipality', 'vbjehuisMunicipalities')
        );
    }

    public function edit(Cooperation $cooperation, Municipality $municipality)
    {
        return view('cooperation.admin.super-admin.municipalities.edit', compact('municipality'));
    }

    public function update(MunicipalityRequest $request, Cooperation $cooperation, Municipality $municipality)
    {
        $data = $request->validated()['municipalities'];
        $municipality->update($data);

        return redirect()->route('cooperation.admin.super-admin.municipalities.index')
            ->with('success', __('cooperation/admin/super-admin/municipalities.update.success'));
    }

    public function couple(MunicipalityCoupleRequest $request, Cooperation $cooperation, Municipality $municipality)
    {
        $data = $request->validated();

        Mapping::whereIn('id', $data['bag_municipalities'])->update([
            'target_model_type' => Municipality::class,
            'target_model_id' => $municipality->id,
        ]);

        $service = MappingService::init()->from($municipality);

        if (! empty($data['vbjehuis_municipality'])) {
            // If not empty, then the request has validated it and we know it's available.
            $parts = explode('-', $data['vbjehuis_municipality'], 2);
            $id = $parts[0] ?? '';
            $name = $parts[1] ?? '';

            $municipalities = RegulationService::init()->getFilters()['Cities'];
            $targetData = Arr::first(Arr::where($municipalities, fn ($a) => $a['Id'] == $id && $a['Name'] == $name));
            $service->sync([$targetData], MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS);
        } else {
            $service->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)->detach();
        }

        return redirect()->route('cooperation.admin.super-admin.municipalities.show', compact('municipality'))
            ->with('success', __('cooperation/admin/super-admin/municipalities.couple.success'));
    }
}
