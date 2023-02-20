@extends('cooperation.admin.layouts.app')

@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.super-admin.municipalities.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" class="btn btn-warning"
                           href="{{route('cooperation.admin.super-admin.municipalities.index')}}">
                            @lang('woningdossier.cooperation.admin.cooperation.questionnaires.create.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.save')
                        </button>
                    </div>
                </div>
                <div class="row alert-top-space">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        @component('layouts.parts.components.form-group', [
                                            'input_name' => 'municipalities.name'
                                        ])
                                            <label for="name">
                                                @lang('cooperation/admin/super-admin/municipalities.form.name.label')
                                            </label>
                                            <input type="text" class="form-control" id="name"
                                                   name="municipalities[name]"
                                                   value="{{ old('municipalities.name') }}"
                                                   placeholder="@lang('cooperation/admin/super-admin/municipalities.form.name.placeholder')">
                                        @endcomponent
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection