<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\MappingHelper;
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
        $vbjehuisMunicipalities = $municipalityService->getAvailableVbjehuisMunicipalities();
        $mappedVbjehuisMunicipality = $municipalityService->retrieveVbjehuisMuncipality();

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

        if (! empty($data['vbjehuis_municipality'])) {
            $municipalities = RegulationService::init()->getFilters()['Cities'];
            $targetData = Arr::first(Arr::where($municipalities, fn ($a) => $a['Id'] === $data['vbjehuis_municipality']));
            MappingService::init()->from($municipality)->sync([$targetData], MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS);
        } else {
            $municipality->mappings()->forType(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)->delete();
        }

        return redirect()->route('cooperation.admin.super-admin.municipalities.show', compact('municipality'))
            ->with('success', __('cooperation/admin/super-admin/municipalities.couple.success'));
    }
}