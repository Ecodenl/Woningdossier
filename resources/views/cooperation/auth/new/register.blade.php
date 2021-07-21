@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20 bg-center bg-cover"
         style="background: url('{{asset('images/background.jpg')}}')" x-data="register()">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10 w-3/4">
            <i class="icon-xxl icon-hoom-logo"></i>
            <h1 class="heading-1">
                @lang('auth.register.form.header')
            </h1>
            <form class="w-full flex flex-wrap justify-center">
                @csrf
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full',
                    'inputName' => 'email',
                ])
                    <input class="form-input" type="text" name="email" value="{{ old('email') }}"
                           placeholder="@lang('auth.register.form.email')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'first_name',
                ])
                    <input class="form-input" type="text" name="first_name" value="{{ old('first_name') }}"
                           placeholder="@lang('auth.register.form.first-name')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'last_name',
                ])
                    <input class="form-input" type="text" name="last_name" value="{{ old('last_name') }}"
                           placeholder="@lang('auth.register.form.last-name')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5  lg:w-1/2 lg:pr-3',
                    'inputName' => 'postal_code',
                ])
                    <input class="form-input" type="text" name="postal_code" value="{{ old('postal_code') }}"
                           placeholder="@lang('auth.register.form.postal-code')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5  lg:w-1/4 lg:px-3',
                    'inputName' => 'number',
                ])
                    <input class="form-input" type="text" name="number" value="{{ old('number') }}"
                           placeholder="@lang('auth.register.form.number')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/4 lg:pl-3',
                    'inputName' => 'house_number_extension',
                ])
                    <input class="form-input" type="text" name="house_number_extension"
                           value="{{ old('house_number_extension') }}"
                           placeholder="@lang('auth.register.form.house-number-extension')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'street',
                ])
                    <input class="form-input" type="text" name="street" value="{{ old('street') }}"
                           placeholder="@lang('auth.register.form.street')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'city',
                ])
                    <input class="form-input" type="text" name="city" value="{{ old('city') }}"
                           placeholder="@lang('auth.register.form.city')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'phone_number',
                ])
                    <input class="form-input" type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="@lang('auth.register.form.phone-number')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'password',
                ])
                    <div class="w-full" x-data="{showPass: false}">
                        <input class="form-input" type="password" name="password"
                               placeholder="@lang('auth.register.form.password')" x-ref="password-input">
                        <i class="icon-sm icon-show absolute right-6 top-5/20 cursor-pointer"
                           x-show="showPass === false"
                           x-on:click="showPass = true; $refs['password-input'].type = 'text'"></i>
                        <i class="icon-sm icon-hide absolute right-6 top-5/20 cursor-pointer"
                           x-show="showPass === true"
                           x-on:click="showPass = false; $refs['password-input'].type = 'password'"></i>
                    </div>
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'password_confirmation',
                ])
                    <input class="form-input" type="password" name="password_confirmation"
                           placeholder="@lang('auth.register.form.password-confirmation')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'password_confirmation',
                ])
                    <div class="checkbox-wrapper">
                        <input id="allow-access" name="allow_access" type="checkbox"
                               @if(! is_null(old('allow_access'))) checked @endif x-model="allowAccess">
                        <label for="allow-access">
                            <span class="checkmark"></span>
                            <span>
                                @lang('conversation-requests.index.form.allow-access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])
                            </span>
                        </label>
                    </div>
                    <p class="text-left">@lang('conversation-requests.index.text')</p>
                @endcomponent

                <button class="btn btn-purple w-full mt-3" x-bind:disabled="! allowAccess">
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