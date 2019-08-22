<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

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
        $this->authorize('create', Cooperation::class);

        return view('cooperation.admin.super-admin.cooperations.create');
    }

    public function store(Cooperation $cooperation, CooperationRequest $request)
    {
        $this->authorize('create', Cooperation::class);
        $cooperationName = $request->get('name');
        $cooperationSlug = $request->get('slug');
        $cooperationWebsiteUrl = $request->get('website_url');

        Cooperation::create([
            'name' => $cooperationName,
            'slug' => $cooperationSlug,
            'website_url' => $cooperationWebsiteUrl,
        ]);

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
        $cooperationName = $request->get('name');
        $cooperationSlug = $request->get('slug');
        $cooperationWebsiteUrl = $request->get('website_url');
        $cooperationId = $request->get('cooperation_id');

        $cooperationToUpdate = Cooperation::find($cooperationId);

        $this->authorize('update', $cooperationToUpdate);
        if ($cooperationToUpdate instanceof Cooperation) {
            $cooperationToUpdate->name = $cooperationName;
            $cooperationToUpdate->slug = $cooperationSlug;
            $cooperationToUpdate->website_url = $cooperationWebsiteUrl;
            $cooperationToUpdate->save();
        }

        return redirect()->route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.cooperations.update.success'));
    }
}
