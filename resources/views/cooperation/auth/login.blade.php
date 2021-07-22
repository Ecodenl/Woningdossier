@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            <i class="icon-xxl icon-hoom-logo"></i>
            <h1 class="heading-1">
                @lang('auth.login.form.header')
            </h1>
            <form class="w-full flex flex-wrap justify-center" method="POST"
                  action="{{ route('cooperation.auth.login', compact('cooperation')) }}">
                @csrf
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full',
                    'inputName' => 'email',
                    'id' => 'email',
                ])
                    <input class="form-input" type="text" name="email" id="email" value="{{ old('email') }}"
                           placeholder="@lang('auth.login.form.email')">
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
                               placeholder="@lang('auth.login.form.enter-password')" x-ref="password-input">
                        <i class="icon-sm icon-show absolute right-6 top-5/20 cursor-pointer" x-show="showPass === false"
                           x-on:click="showPass = true; $refs['password-input'].type = 'text'"></i>
                        <i class="icon-sm icon-hide absolute right-6 top-5/20 cursor-pointer" x-show="showPass === true"
                           x-on:click="showPass = false; $refs['password-input'].type = 'password'"></i>
                    </div>
                @endcomponent
                <button class="btn btn-purple w-full mt-6">
                    @lang('auth.login.form.submit')
                </button>
            </form>
            <p>
                <a href="{{ route('cooperation.auth.password.request.index', compact('cooperation')) }}">
                    @lang('auth.login.form.forgot-password')
                </a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="{{ route('cooperation.register', compact('cooperation')) }}">
                    @lang('auth.register.form.header')
                </a>
            </p>
        </div>
        <div class="mt-5 text-center">
            <a href="{{ route('cooperation.privacy.index', compact('cooperation')) }}" class="text-white">
                @lang('default.privacy-policy')
            </a>
            <span class="text-white">|</span>
            <a href="{{ route('cooperation.disclaimer.index', compact('cooperation')) }}" class="text-white">
                @lang('default.terms-and-conditions')
            </a>
        </div>
    </div>
@endsection