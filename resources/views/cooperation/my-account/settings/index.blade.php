@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <?php $building = $user->buildings->first(); ?>
                    <div class="panel-heading">
                        @lang('woningdossier.cooperation.my-account.settings.form.index.header')
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.settings.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">@lang('auth.register.form.e-mail')</label>

                                <div class="col-md-8">
                                    {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required autofocus>--}}
                                    <span>{{ $user->email }}</span>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">@lang('auth.register.form.connected-address')</label>

                                <div class="col-md-8">
                                    {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required autofocus>--}}
                                    <span>{{$building->street}} {{$building->number}} {{$building->extension}}</span>
                                    <br>
                                    <span>{{$building->postal_code}} {{$building->city}} </span>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                <label for="first_name" class="col-md-4 control-label">@lang('auth.register.form.first_name')</label>

                                <div class="col-md-8">
                                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" required autofocus>

                                    @if ($errors->has('first_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <label for="last_name" class="col-md-4 control-label">@lang('auth.register.form.last_name')</label>

                                <div class="col-md-8">
                                    <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required autofocus>

                                    @if ($errors->has('last_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                <label for="phone_number" class="col-md-4 control-label">@lang('auth.register.form.phone_number')</label>

                                <div class="col-md-8">
                                    <input id="phone_number" type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" autofocus>

                                    @if ($errors->has('phone_number'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                                <label for="current_password" class="col-md-4 control-label">@lang('auth.register.form.current_password')</label>

                                <div class="col-md-8">
                                    <input id="current_password" type="password" class="form-control" name="current_password">

                                    @if ($errors->has('current_password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">@lang('auth.register.form.new_password')</label>

                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control" name="password">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">@lang('auth.register.form.new_password_confirmation')</label>

                                <div class="col-md-8">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.my-account.settings.form.index.submit')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.form.reset-file.header')</div>

                    <div class="panel-body">
                        @lang('woningdossier.cooperation.my-account.settings.form.reset-file.description')
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.settings.reset-file', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="reset-file" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.form.reset-file.label')</label>
                                <div class="col-md-8">
                                    <a id="reset-account" class="btn btn-danger">
                                        @lang('woningdossier.cooperation.my-account.settings.form.reset-file.submit')
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.form.destroy.header')</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.settings.destroy', ['cooperation' => $cooperation]) }}">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="delete-account" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.form.destroy.label')</label>
                                <div class="col-md-8">
                                    <button type="submit" id="delete-account" class="btn btn-danger">
                                        @lang('woningdossier.cooperation.my-account.settings.form.destroy.submit')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection


@push('js')
    <script>
        var areYouSure = '@lang('woningdossier.cooperation.my-account.settings.form.reset-file.are-you-sure')';
        $('#reset-account').click(function () {
            if (confirm(areYouSure)) {
                $(this).closest('form').submit();
            } else {
                return false;
                event.preventDefault();
            }
        });
        var areYouSureToDestroy = '@lang('woningdossier.cooperation.my-account.settings.form.destroy.are-you-sure')';
        $('#delete-account').click(function () {
            if (confirm(areYouSureToDestroy)) {
                $(this).closest('form').submit();
            } else {
                return false;
                event.preventDefault();
            }
        })
    </script>
@endpush