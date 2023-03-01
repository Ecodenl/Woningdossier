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
        $vbjehuisMunicipalities = Wrapper::wrapCall(fn () => RegulationService::init()->getFilters()['Cities']) ?? [];

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

        $bagIds = [];
        $newBags = [];
        foreach ($data['bag_municipalities'] as $value) {
            if (is_numeric($value)) {
                $bagIds[] = $value;
            } else {
                // No further validation. We expect the admin to be smart and not fill in already used municipalities.
                // If they do, it might mess up for the users, but then they are to blame :)
                $newBags[] = [
                    'type' => MappingHelper::TYPE_BAG_MUNICIPALITY,
                    'from_value' => $value,
                    'target_model_type' => Municipality::class,
                    'target_model_id' => $municipality->id,
                ];
            }
        }

        // Clear all related first.
        Mapping::where('target_model_type', Municipality::class)
            ->where('target_model_id', $municipality->id)
            ->forType(MappingHelper::TYPE_BAG_MUNICIPALITY)
            ->update([
                'target_model_type' => null,
                'target_model_id' => null,
            ]);

        // If IDs, link IDs.
        if (! empty($bagIds)) {
            Mapping::whereIn('id', $bagIds)->update([
                'target_model_type' => Municipality::class,
                'target_model_id' => $municipality->id,
            ]);
        }

        // If user input, create new rows.
        if (! empty($newBags)) {
            // We don't need model events.
            Mapping::insert($newBags);
        }

        // Add or unlink vbjehuis municipality.
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
