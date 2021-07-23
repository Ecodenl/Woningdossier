@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.reset.form.header')
            </h1>
            @if(session('token_invalid'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'red'])
                    {!! session('token_invalid') !!}
                @endcomponent
            @endif
            @if(!session()->has('token_invalid'))
                <form class="w-full flex flex-wrap justify-center" method="POST"
                      action="{{ route('cooperation.auth.password.reset.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'withInputSource' => false,
                        'class' => 'w-full',
                        'inputName' => 'email',
                        'id' => 'email',
                    ])
                        <input class="form-input" type="text" name="email" id="email" value="{{ old('email') }}"
                               placeholder="@lang('auth.register.form.email')">
                        <i class="icon-sm icon-mail-green absolute right-6 top-5/20"></i>
                    @endcomponent
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'withInputSource' => false,
                        'class' => 'w-full -mt-5',
                        'inputName' => 'password',
                        'id' => 'password',
                    ])
                        <div class="w-full" x-data="{showPass: false}">
                            <input class="form-input" type="password" name="password" id="password"
                                   placeholder="@lang('auth.register.form.new-password')" x-ref="password-input">
                            <i class="icon-sm icon-show absolute right-6 top-5/20 cursor-pointer" x-show="showPass === false"
                               x-on:click="showPass = true; $refs['password-input'].type = 'text'"></i>
                            <i class="icon-sm icon-hide absolute right-6 top-5/20 cursor-pointer" x-show="showPass === true"
                               x-on:click="showPass = false; $refs['password-input'].type = 'password'"></i>
                        </div>
                    @endcomponent
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'withInputSource' => false,
                        'class' => 'w-full -mt-5',
                        'inputName' => 'password_confirmation',
                        'id' => 'password-confirmation',
                    ])
                        <input class="form-input" type="password" name="password_confirmation" id="password-confirmation"
                               placeholder="@lang('auth.register.form.new-password-confirmation')">
                    @endcomponent
                    <button class="btn btn-purple w-full mt-6">
                        @lang('auth.reset.form.set-password')
                    </button>
                </form>
            @endif
            <p>
                <a href="{{ route('cooperation.auth.login') }}">
                    @lang('auth.login.form.header')
                </a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="{{ route('cooperation.register') }}">
                    @lang('auth.register.form.header')
                </a>
            </p>
        </div>
    </div>
@endsection