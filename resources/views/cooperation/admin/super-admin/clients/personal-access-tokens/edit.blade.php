@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/clients/personal-access-tokens.edit.header', ['client_name' => $client->name])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.update', compact('client', 'personalAccessToken'))}}">
                        @csrf
                        @method('PUT')

                        @component('layouts.parts.components.form-group', ['input_name' => 'personal_access_tokens.name'])
                            <label for="">@lang('cooperation/admin/super-admin/clients/personal-access-tokens.column-translations.name')</label>
                            <input required="required" type="text" name="personal_access_tokens[name]" value="{{old('personal_access_tokens.name', $personalAccessToken->name)}}" class="form-control">
                        @endcomponent


                        <h2>Bevoegdheden</h2>
                        @component('layouts.parts.components.form-group', ['input_name' => 'personal_access_tokens.abilities.access'])
                            <label for="">@lang('cooperation/admin/super-admin/clients/personal-access-tokens.edit.cooperations')</label>
                            <select class="select2 form-control" name="personal_access_tokens[abilities][]" id="access" multiple="multiple">
                                @foreach($cooperations as $cooperation)
                                    <option @if($personalAccessToken->can("access:{$cooperation->slug}")) selected="selected" @endif value="{{"access:{$cooperation->slug}"}}">{{$cooperation->name}}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <button class="btn btn-primary">
                            @lang('cooperation/admin/super-admin/clients/personal-access-tokens.edit.submit')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('select#access').select2();
        });
    </script>
@endpush