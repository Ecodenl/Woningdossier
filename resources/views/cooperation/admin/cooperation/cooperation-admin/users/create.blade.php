@extends('cooperation.admin.cooperation.cooperation-admin.layouts.app')

@section('cooperation_admin_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.cooperation-admin.users.store')}}" method="post"  >
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label for="first_name">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.first-name')</label>
                                    <input  type="text" value="{{old('first_name')}}" class="form-control" name="first_name" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.first-name')...">
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
                                    <label for="last_name">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.last-name')</label>
                                    <input  type="text" value="{{old('last_name')}}"class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.last-name')..." name="last_name">
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
                                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.password.header')
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="password" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} ">
                                                <label for="password">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.password.label')</label>
                                                <input id="password" type="password" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.password.placeholder')" name="password">
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @else
                                                    <span class="help-block">
                                                        <strong>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.password.help')</strong>
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
                                    <label for="email">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.email')</label>
                                    <input  type="email" value="{{old('email')}}" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.email')..." name="email">
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
                                <div class="form-group" {{ $errors->has('roles') ? ' has-error' : '' }}>
                                    <label for="roles">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.roles')</label>
                                    <select name="roles[]" class="roles form-control" id="roles" multiple="multiple">
                                        @foreach($roles as $role)
                                            <option @if(old('roles') == $role->id) selected @endif value="{{$role->id}}">{{$role->human_readable_name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('roles'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('roles') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection




@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@push('js')
    <script src="{{asset('js/select2.js')}}"></script>

    <script>

        $('form').disableAutoFill();


        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });


        $(document).ready(function () {

            $(".roles").select2({
                placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.select-role')",
                maximumSelectionLength: Infinity
            });
        });
    </script>
@endpush
