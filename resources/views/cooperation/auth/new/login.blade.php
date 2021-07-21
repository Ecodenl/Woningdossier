@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-screen h-screen flex justify-center items-center flex-col"
         style="background: url('{{ asset('images/background.jpg') }}')">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            <i class="icon-xxl icon-hoom-logo"></i>
            <h1 class="heading-1">
                @lang('auth.login.form.header')
            </h1>
            <form class="w-full flex flex-wrap justify-center">
                <div class="input-group">
                    <input class="form-input" type="text" name="email" placeholder="@lang('auth.login.form.email')">
                    <i class="icon-sm icon-mail-green absolute right-6 top-5/20"></i>
                </div>
                <div class="input-group">
                    <input class="form-input" type="password" name="password" placeholder="@lang('auth.login.form.enter-password')">
                    <i class="icon-sm icon-show absolute right-6 top-5/20"></i>
                </div>
                <button class="btn btn-purple w-full mt-3">
                    Log in
                </button>
            </form>
            <p>
                <a href="#">@lang('auth.login.form.forgot-password')</a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="#">@lang('auth.register.form.header')</a>
            </p>
        </div>
        <div class="mt-5 text-center">
            <a href="#" class="text-white">@lang('default.privacy-policy')</a>
            <span class="text-white">|</span>
            <a href="#" class="text-white">@lang('default.terms-and-conditions')</a>
        </div>
    </div>
@endsection