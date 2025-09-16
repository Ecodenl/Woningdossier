@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/clients.index.header')
])

@section('content')
    <form class="flex flex-wrap w-full"
          action="{{route('cooperation.admin.super-admin.clients.update', compact('client'))}}"
          method="POST">
        @csrf
        @method('PUT')

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
            'label' => __('cooperation/admin/super-admin/clients.column-translations.name'),
            'id' => 'client-name',
            'inputName' => "clients.name",
            'withInputSource' => false,
        ])
            <input id="client-name" required="required" type="text" name="clients[name]"
                   value="{{old('clients.name', $client->name)}}" class="form-input">
        @endcomponent

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('cooperation/admin/super-admin/clients.edit.form.submit')
            </button>
        </div>
    </form>
@endsection