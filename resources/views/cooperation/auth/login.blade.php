@extends('cooperation.layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if(session()->has('account_connected'))
                @component('cooperation.tool.components.alert')
                    {{session('account_connected')}}
                @endcomponent
            @endif

            @if(session('verified'))
                @component('cooperation.tool.components.alert')
                    @lang('cooperation/auth/verify.success-log-in')
                @endcomponent
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">@lang('auth.login.form.header')</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('cooperation.auth.login', ['cooperation' => $cooperation]) }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">@lang('auth.login.form.e-mail')</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('auth.login.form.password')</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('auth.login.form.remember_me')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('auth.login.form.button')
                                </button>

                                <a class="btn btn-link" href="{{ route('cooperation.auth.password.request.index', ['cooperation' => $cooperation]) }}">
                                    @lang('auth.login.form.forgot_password')
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
