<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\CooperationRequest;
use App\Models\Cooperation;
use App\Services\UserService;
use Illuminate\Support\Facades\Crypt;

class CooperationController extends Controller
{
    public function index(Cooperation $currentCooperation)
    {
        $cooperations = Cooperation::all();

        return view('cooperation.admin.super-admin.cooperations.index', compact('cooperations', 'currentCooperation'));
    }

    public function create()
    {
        $this->authorize('create', Cooperation::class);

        return view('cooperation.admin.super-admin.cooperations.create');
    }

    public function store(Cooperation $cooperation, CooperationRequest $request)
    {
        $this->authorize('create', Cooperation::class);

        $data = $request->all();
        if (! empty($data['cooperations']['econobis_api_key'])) {
            $data['econobis_api_key'] = Crypt::encrypt($data['cooperations']['econobis_api_key']);
        }
        unset($data['cooperations']['econobis_api_key']);
        Cooperation::create($data);

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.store.success'));
    }

    public function edit(Cooperation $currentCooperation, Cooperation $cooperationToEdit)
    {
        $this->authorize('edit', $cooperationToEdit);

        return view('cooperation.admin.super-admin.cooperations.edit', compact('cooperationToEdit'));
    }

    public function update(Cooperation $cooperation, CooperationRequest $request)
    {
        $cooperationId = $request->get('cooperation_id');

        $cooperationToUpdate = Cooperation::find($cooperationId);

        $this->authorize('update', $cooperationToUpdate);
        if ($cooperationToUpdate instanceof Cooperation) {
            $data = $request->all();
            if (! empty($data['cooperations']['econobis_api_key'])) {
                $data['econobis_api_key'] = Crypt::encrypt($data['cooperations']['econobis_api_key']);
            }
            unset($data['cooperations']['econobis_api_key']);
            $cooperationToUpdate->fill($data);
            $cooperationToUpdate->save();
        }

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.update.success'));
    }

    public function destroy(Cooperation $cooperation, Cooperation $cooperationToDestroy)
    {
        $this->authorize('delete', $cooperationToDestroy);

        $exampleBuildings = $cooperationToDestroy->exampleBuildings;

        foreach ($exampleBuildings as $exampleBuilding) {
            $exampleBuilding->contents()->delete();
        }

        $cooperationToDestroy->exampleBuildings()->delete();

        $users = $cooperationToDestroy->users()->withoutGlobalScopes()->get();
        foreach ($users as $user) {
            UserService::deleteUser($user, true);
        }

        $cooperationToDestroy->delete();

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.destroy.success'));
    }
}
