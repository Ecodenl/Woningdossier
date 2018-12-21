@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.header', ['firstName' => $user->first_name, 'lastName' => $user->last_name])</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.coordinator.assign-roles.update', ['userId' => $user->id])}}" method="post" autocomplete="off">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('first_name') ? ' has-error' : '' }}>
                                    <label for="first_name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.first-name')</label>
                                    <input disabled value="{{$user->first_name}}"  type="text" class="form-control" name="first_name" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.first-name')...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('last_name') ? ' has-error' : '' }}>
                                    <label for="last_name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.last-name')</label>
                                    <input disabled value="{{$user->last_name}}"  type="text" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.last-name')..." name="last_name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('email') ? ' has-error' : '' }}>
                                    <label for="email">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.email')</label>
                                    <input disabled value="{{$user->email}}"  type="email" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.email')..." name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('roles') ? ' has-error' : '' }}>
                                    <label for="roles">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.roles')</label>
                                    <select name="roles[]" class="roles form-control" id="roles" multiple="multiple">
                                        @foreach($roles as $role)
                                            <option @if(old('roles') == $role->id) selected @elseif($user->hasRole($role->name)) selected @endif value="{{$role->id}}">{{$role->human_readable_name}}</option>
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
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
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

            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
            });

            $(document).ready(function () {
                $(".roles").select2({
                    placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.assign-roles.edit.form.select-role')",
                    maximumSelectionLength: Infinity
                });
            });
        </script>
    @endpush

