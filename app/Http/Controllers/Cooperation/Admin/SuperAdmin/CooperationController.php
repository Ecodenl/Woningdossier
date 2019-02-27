<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\CooperationRequest;
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
        return view('cooperation.admin.super-admin.cooperations.create');
    }

    public function store(Cooperation $cooperation, CooperationRequest $request)
    {
        $cooperationName = $request->get('name');
        $cooperationSlug = $request->get('slug');

        Cooperation::create([
            'name' => $cooperationName,
            'slug' => $cooperationSlug,
        ]);

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.store.success'));
    }

    public function edit(Cooperation $currentCooperation, $cooperationIdToEdit)
    {
        $cooperationToEdit = Cooperation::find($cooperationIdToEdit);

        return view('cooperation.admin.super-admin.cooperations.edit', compact('cooperationToEdit'));
    }

    public function update(Cooperation $cooperation, CooperationRequest $request)
    {
        $cooperationName = $request->get('name');
        $cooperationSlug = $request->get('slug');
        $cooperationId = $request->get('cooperation_id');

        $cooperationToEdit = Cooperation::find($cooperationId);

        if ($cooperationToEdit instanceof Cooperation) {
            $cooperationToEdit->name = $cooperationName;
            $cooperationToEdit->slug = $cooperationSlug;
            $cooperationToEdit->save();
        }

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.update.success'));
    }
}
