<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\ClientFormRequest;
use App\Models\Client;
use App\Models\Cooperation;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::all();

        return view('cooperation.admin.super-admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('cooperation.admin.super-admin.clients.create');
    }

    public function store(ClientFormRequest $request): RedirectResponse
    {
        $name = $request->input('clients.name');
        $short = Str::slug($name);
        $client = Client::create(compact('name', 'short'));

        return to_route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients.store.success'));
    }

    public function edit(Cooperation $cooperation, Client $client): View
    {
        return view('cooperation.admin.super-admin.clients.edit', compact('client'));
    }

    public function update(ClientFormRequest $request, Cooperation $cooperation, Client $client): RedirectResponse
    {
        $name = $request->input('clients.name');
        $short = Str::slug($name);
        $client->update(compact('name', 'short'));


        return to_route('cooperation.admin.super-admin.clients.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients.update.success'));
    }

    public function destroy(Cooperation $cooperation, Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);

        $client->delete();

        return to_route('cooperation.admin.super-admin.clients.index')
            ->with('success', __('cooperation/admin/super-admin/clients.destroy.success'));
    }
}
