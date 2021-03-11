@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/clients.create.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="{{route('cooperation.admin.super-admin.clients.store')}}">
                        @csrf
                        
                        @component('layouts.parts.components.form-group', ['input_name' => 'personal_access_tokens.name'])
                            <label for="">@lang('cooperation/admin/super-admin/clients.column-translations.name')</label>
                            <input required="required" type="text" name="clients[name]" value="{{old('clients.name')}}" class="form-control">
                        @endcomponent

                        <button class="btn btn-primary">
                            @lang('cooperation/admin/super-admin/clients.create.form.submit')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
