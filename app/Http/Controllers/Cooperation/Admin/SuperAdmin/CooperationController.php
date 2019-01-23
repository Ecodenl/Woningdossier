<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Requests\Cooperation\Admin\SuperAdmin\CooperationRequest;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CooperationController extends Controller
{
    public function index()
    {
        $cooperations = Cooperation::all();

        return view('cooperation.admin.super-admin.cooperations.index', compact('cooperations'));
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

    public function edit()
    {

    }

    public function update(Cooperation $cooperation, CooperationRequest $request)
    {

    }


}
