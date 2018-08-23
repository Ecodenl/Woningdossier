@extends('cooperation.admin.cooperation.cooperation-admin.layouts.app')

@section('cooperation_admin_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.cooperation-admin.coordinator.store')}}" method="post"  >
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label for="first_name">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.first-name')</label>
                                    <input  type="text" value="{{old('first_name')}}" class="form-control" name="first_name" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.first-name')...">
                                    @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    <label for="last_name">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.last-name')</label>
                                    <input  type="text" value="{{old('last_name')}}"class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.last-name')..." name="last_name">
                                    @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" href="#password">
                                                <span class="glyphicon glyphicon-plus"></span>
                                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.password.header')
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="password" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} ">
                                                <label for="password">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.password.label')</label>
                                                <input id="password" type="password" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.password.placeholder')" name="password">
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @else
                                                    <span class="help-block">
                                                        <strong>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.password.help')</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.email')</label>
                                    <input  type="email" value="{{old('email')}}" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.email')..." name="email">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.coordinator.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection





@push('js')

    <script>

        $('form').disableAutoFill();

        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });


    </script>
@endpush

