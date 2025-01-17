<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Helpers\Models\CooperationHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationController extends Controller
{
    public function index(Cooperation $currentCooperation)
    {
        $cooperations = Cooperation::all();

        return view('cooperation.admin.super-admin.cooperations.index', compact('cooperations', 'currentCooperation'));
    }

    public function create()
    {
        $this->authorize('updateOrCreate', Cooperation::class);

        return view('cooperation.admin.super-admin.cooperations.create');
    }


    public function edit(Cooperation $cooperation, Cooperation $cooperationToEdit)
    {
        $this->authorize('updateOrCreate', $cooperationToEdit);

        return view('cooperation.admin.super-admin.cooperations.edit', compact('cooperationToEdit'));
    }


    public function destroy(Cooperation $cooperation, Cooperation $cooperationToDestroy)
    {
        $this->authorize('delete', $cooperationToDestroy);

        CooperationHelper::destroyCooperation($cooperationToDestroy);

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.destroy.success'));
    }
}
