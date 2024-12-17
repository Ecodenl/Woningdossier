<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Client;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\PersonalAccessTokenFormRequest;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\PersonalAccessToken;

class PersonalAccessTokenController extends Controller
{
    public function index(Cooperation $cooperation, Client $client): View
    {
        $client->load('tokens');

        return view('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('cooperation', 'client'));
    }

    public function create(Cooperation $cooperation, Client $client): View
    {
        $cooperations = Cooperation::all();
        return view('cooperation.admin.super-admin.clients.personal-access-tokens.create', compact('cooperation', 'client', 'cooperations'));
    }

    public function edit(Cooperation $cooperation, Client $client, PersonalAccessToken $personalAccessToken): View
    {
        $cooperations = Cooperation::all();
        return view('cooperation.admin.super-admin.clients.personal-access-tokens.edit', compact('cooperation', 'client', 'cooperations', 'personalAccessToken'));
    }

    public function store(PersonalAccessTokenFormRequest $request, Cooperation $cooperation, Client $client): RedirectResponse
    {
        $newAccessToken = $client->createToken(
            $request->input('personal_access_tokens.name'),
            $request->input('personal_access_tokens.abilities', ['*'])
        );

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('token', $newAccessToken);
    }

    public function update(PersonalAccessTokenFormRequest $request, Cooperation $cooperation, Client $client, PersonalAccessToken $personalAccessToken): RedirectResponse
    {
        $personalAccessToken->update([
            'name' => $request->input('personal_access_tokens.name'),
            'abilities' => $request->input('personal_access_tokens.abilities', ['*'])
        ]);

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients/personal-access-tokens.update.success'));
    }

    public function destroy(Cooperation $cooperation, Client $client, PersonalAccessToken $personalAccessToken): RedirectResponse
    {
        $personalAccessToken->delete();

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients/personal-access-tokens.destroy.success'));
    }
}
