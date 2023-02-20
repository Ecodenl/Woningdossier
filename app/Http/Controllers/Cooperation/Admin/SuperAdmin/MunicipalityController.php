<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\MunicipalityRequest;
use App\Models\Cooperation;
use App\Models\Municipality;
use App\Services\Models\MunicipalityService;

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

        return redirect()->route('cooperation.admin.super-admin.municipalities.show', compact('cooperation', 'municipality'))
            ->with('success', __('cooperation/admin/super-admin/municipalities.store.success'));
    }

    public function show(Cooperation $cooperation, Municipality $municipality, MunicipalityService $service)
    {
        $service->forMunicipality($municipality);
        $bagMunicipalities = $service->retrieveBagMunicipalities();
        $vbjehuisMunicipality = $service->retrieveVbjehuisMuncipality();

        return view(
            'cooperation.admin.super-admin.municipalities.show',
            compact('municipality', 'bagMunicipalities', 'vbjehuisMunicipality')
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

    public function destroy(Cooperation $cooperation, Municipality $municipality)
    {
        $municipality->delete();

        return redirect()->route('cooperation.admin.super-admin.municipalities.show')
            ->with('success', __('cooperation/admin/super-admin/municipalities.destroy.success'));
    }
}
