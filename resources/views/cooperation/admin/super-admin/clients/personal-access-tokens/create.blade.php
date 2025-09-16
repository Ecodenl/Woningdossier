@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/clients/personal-access-tokens.create.header', ['client_name' => $client->name])
])

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.store', compact('client'))}}"
          method="POST">
        @csrf

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3 required',
            'label' => __('cooperation/admin/super-admin/clients/personal-access-tokens.column-translations.name'),
            'id' => 'token-name',
            'inputName' => "personal_access_tokens.name",
            'withInputSource' => false,
        ])
            <input id="token-name" required="required" type="text" name="personal_access_tokens[name]"
                   value="{{old('personal_access_tokens.name')}}" class="form-input">
        @endcomponent

        <h2 class="heading-2 w-full mt-5 mb-2">
            @lang('cooperation/admin/super-admin/clients/personal-access-tokens.form.permissions.header')
        </h2>
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
            'label' => __('cooperation/admin/super-admin/clients/personal-access-tokens.form.permissions.label'),
            'id' => 'token-name',
            'inputName' => "personal_access_tokens.abilities[]",
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select class="form-input hidden" name="personal_access_tokens[abilities][]" multiple>
                    @foreach($cooperations as $cooperation)
                        <option value="{{"access:{$cooperation->slug}"}}">
                            {{$cooperation->name}}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent

        <div class="w-full">
            <button class="btn btn-green">
                @lang('cooperation/admin/super-admin/clients/personal-access-tokens.create.submit')
            </button>
        </div>
    </form>
@endsection
