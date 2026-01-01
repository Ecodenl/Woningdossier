<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Jobs\DestroyCooperation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CooperationController extends Controller
{
    public function index(Cooperation $currentCooperation): View
    {
        $cooperations = Cooperation::all();

        return view('cooperation.admin.super-admin.cooperations.index', compact('cooperations', 'currentCooperation'));
    }

    public function create(): View
    {
        Gate::authorize('create', Cooperation::class);

        return view('cooperation.admin.super-admin.cooperations.create');
    }

    public function edit(Cooperation $cooperation, Cooperation $cooperationToEdit): View
    {
        Gate::authorize('update', $cooperationToEdit);

        return view('cooperation.admin.super-admin.cooperations.edit', compact('cooperationToEdit'));
    }

    public function destroy(Cooperation $cooperation, Cooperation $cooperationToDestroy): RedirectResponse
    {
        Gate::authorize('delete', $cooperationToDestroy);

        // Soft delete.
        $cooperationToDestroy->delete();

        // Dispatch job to handle the rest.
        DestroyCooperation::dispatch($cooperationToDestroy);

        return to_route('cooperation.admin.super-admin.cooperations.index')
            ->with('success', __('cooperation/admin/super-admin/cooperations.destroy.success'));
    }
}
