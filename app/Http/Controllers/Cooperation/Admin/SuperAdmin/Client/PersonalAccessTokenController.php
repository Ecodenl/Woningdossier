<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;

class PersonalAccessTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation, Client $client)
    {
        $client->load('tokens');
        return view('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('cooperation', 'client'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Cooperation $cooperation, Client $client)
    {
        return view('cooperation.admin.super-admin.clients.personal-access-tokens.create', compact('cooperation', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cooperation $cooperation, Client $client)
    {
        $newAccessToken = $client->createToken($request->input('personal_access_tokens.name'));

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('token', $newAccessToken);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cooperation $cooperation, Client $client, PersonalAccessToken $personalAccessToken)
    {
        $personalAccessToken->delete();

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients/personal-access-tokens.destroy.success'));
    }
}
