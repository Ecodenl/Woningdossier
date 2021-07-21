@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20 bg-center bg-cover"
         style="background: url('{{asset('images/background.jpg')}}')">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10 w-3/4">
            <i class="icon-xxl icon-hoom-logo"></i>
            <h1 class="heading-1">
                @lang('auth.register.form.header')
            </h1>
            <form class="w-full flex flex-wrap justify-center">
                @csrf
                <div class="input-group">
                    <input class="form-input" type="text" name="email" placeholder="@lang('auth.register.form.email')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="first_name"
                           placeholder="@lang('auth.register.form.first-name')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="last_name"
                           placeholder="@lang('auth.register.form.last-name')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="postal_code"
                           placeholder="@lang('auth.register.form.postal-code')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="number"
                           placeholder="@lang('auth.register.form.number')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="house_number_extension"
                           placeholder="@lang('auth.register.form.house-number-extension')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="street"
                           placeholder="@lang('auth.register.form.street')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="city"
                           placeholder="@lang('auth.register.form.city')">
                </div>
                <div class="input-group">
                    <input class="form-input" type="text" name="phone_number"
                           placeholder="@lang('auth.register.form.phone-number')">
                </div>
                <div class="input-group" x-data="{showPass: false}">
                    <input class="form-input" type="password" name="password"
                           placeholder="@lang('auth.register.form.password')" x-ref="password-input">
                    <i class="icon-sm icon-show absolute right-6 top-5/20 cursor-pointer" x-show="showPass === false"
                       x-on:click="showPass = true; $refs['password-input'].type = 'text'"></i>
                    <i class="icon-sm icon-hide absolute right-6 top-5/20 cursor-pointer" x-show="showPass === true"
                       x-on:click="showPass = false; $refs['password-input'].type = 'password'"></i>
                </div>
                <div class="input-group">
                    <input class="form-input" type="password" name="password_confirmation"
                           placeholder="@lang('auth.register.form.password-confirmation')">
                </div>
                <button class="btn btn-purple w-full mt-3">
                    @lang('auth.register.form.submit')
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