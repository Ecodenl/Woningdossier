<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\ClientFormRequest;
use App\Models\Client;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();

        return view('cooperation.admin.super-admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cooperation.admin.super-admin.clients.create');
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
    public function edit(Cooperation $cooperation, Client $client)
    {
        return view('cooperation.admin.super-admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientFormRequest $request, Cooperation $cooperation, Client $client)
    {
        $name = $request->input('clients.name');
        $short = Str::slug($name);
        $client->update(compact('name', 'short'));


        return redirect()
            ->route('cooperation.admin.super-admin.clients.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients.update.success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(ClientFormRequest $request)
    {
        $name = $request->input('clients.name');
        $short = Str::slug($name);
        $client = Client::create(compact('name', 'short'));

        return redirect()
            ->route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('client'))
            ->with('success', __('cooperation/admin/super-admin/clients.store.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
