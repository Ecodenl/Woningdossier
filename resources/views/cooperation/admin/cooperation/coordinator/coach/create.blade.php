@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.coordinator.coach.store')}}" method="post"  >
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('first_name') ? ' has-error' : '' }}>
                                    <label for="first_name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.first-name')</label>
                                    <input  type="text" class="form-control" name="first_name" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.first-name')...">
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
                                <div class="form-group" {{ $errors->has('last_name') ? ' has-error' : '' }}>
                                    <label for="last_name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.last-name')</label>
                                    <input  type="text" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.last-name')..." name="last_name">
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
                                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.password.header')
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="password" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} ">
                                                <label for="password">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.password.label')</label>
                                                <input id="password" type="password" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.password.placeholder')" name="password">
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @else
                                                    <span class="help-block">
                                                        <strong>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.password.help')</strong>
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
                                <div class="form-group" {{ $errors->has('email') ? ' has-error' : '' }}>
                                    <label for="email">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.email')</label>
                                    <input  type="email" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.email')..." name="email">
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
                                    <label for="roles">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.roles')</label>
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
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
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
        function makeid() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for (var i = 0; i < 5; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        }


        // find all the inputs inside a form
        var inputs = $('form').find('input');

        // generate 1 new input name
        var newInputName = makeid();

        // objects for the fake and original names
        var fakeNames = {};
        var originalNames = {};

        // loop through the inputs, collect the original name and set a fake name
        $(inputs).each(function (index, value) {

            // set original name
            originalNames[index] = $(this).attr("name");
            // set the fake name on the input
            $(this).attr('name', newInputName);
            // collect the fakename
            fakeNames[index] = $(this).attr('name');

        });

        // on submit put the original names back to the inputs
        // so we dont have a problem in the backend
        $('form').on('submit', function () {
            Object.keys(fakeNames).forEach(function(key) {
                $(inputs[key]).attr('name', originalNames[key])
            });
        });

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

