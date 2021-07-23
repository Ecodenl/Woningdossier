@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20 "
         x-data="register('{{route('cooperation.check-existing-email', compact('cooperation'))}}')">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10 w-3/4">
            <i class="icon-xxl icon-hoom-logo"></i>
            <h1 class="heading-1">
                @lang('auth.register.form.header')
            </h1>
            @if(session('success'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('success') }}
                @endcomponent
            @endif
            <form class="w-full flex flex-wrap justify-center" method="POST" id="register"
                  action="{{ route('cooperation.register', compact('cooperation')) }}"
                  x-data="picoAddress('{{ route('api.get-address-data') }}')">
                @csrf
                <input type="hidden" name="addressid" x-bind="addressId" value="{{ old('addressid') }}">
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full',
                    'inputName' => 'email',
                ])
                    <input class="form-input" type="text" name="email" value="{{ old('email') }}"
                           placeholder="@lang('auth.register.form.email')" x-on:change="checkEmail($el)">
                    <p class="text-red" x-show="showEmailWarning">
                        @lang('auth.register.form.possible-wrong-email')
                    </p>
                    <hr class="w-full h-0 invisible">
                    <p class="text-blue-800" x-show="alreadyMember">
                        @lang('auth.register.form.already-member')
                    </p>
                    <hr class="w-full h-0 invisible">
                    <p class="text-blue-800" x-show="emailExists">
                        @lang('auth.register.form.email-exists')
                    </p>
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'first_name',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="first_name" value="{{ old('first_name') }}"
                           placeholder="@lang('auth.register.form.first-name')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'last_name',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="last_name" value="{{ old('last_name') }}"
                           placeholder="@lang('auth.register.form.last-name')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5  lg:w-1/2 lg:pr-3',
                    'inputName' => 'postal_code',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="postal_code" value="{{ old('postal_code') }}"
                           placeholder="@lang('auth.register.form.postal-code')" x-bind="postcode">
                    <p class="text-blue-800 -mt-2" x-show="showPossibleError">
                        @lang('auth.register.form.possible-wrong-postal-code')
                    </p>
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5  lg:w-1/4 lg:px-3',
                    'inputName' => 'number',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="number" value="{{ old('number') }}"
                           placeholder="@lang('auth.register.form.number')" x-bind="houseNumber">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/4 lg:pl-3',
                    'inputName' => 'house_number_extension',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="house_number_extension"
                           value="{{ old('house_number_extension') }}"
                           placeholder="@lang('auth.register.form.house-number-extension')"
                           x-bind="houseNumberExtension">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'street',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="street" value="{{ old('street') }}"
                           placeholder="@lang('auth.register.form.street')" x-bind="street">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'city',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="city" value="{{ old('city') }}"
                           placeholder="@lang('auth.register.form.city')" x-bind="city">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'phone_number',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="@lang('auth.register.form.phone-number')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'password',
                    'attr' => 'x-show="! alreadyMember && ! emailExists"',
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
                    'attr' => 'x-show="! alreadyMember && ! emailExists"',
                ])
                    <input class="form-input" type="password" name="password_confirmation"
                           placeholder="@lang('auth.register.form.password-confirmation')">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'allow_access',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <div class="checkbox-wrapper">
                        <input id="allow-access" name="allow_access" type="checkbox" value="1" x-model="allowAccess">
                        <label for="allow-access">
                            <span class="checkmark"></span>
                            <span>
                                @lang('conversation-requests.index.form.allow-access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])
                            </span>
                        </label>
                    </div>
                    <p class="text-left">@lang('conversation-requests.index.text')</p>
                @endcomponent

                <button class="btn btn-purple w-full mt-3" type="submit" x-bind:disabled="! allowAccess || alreadyMember">
                    @lang('auth.register.form.submit')
                </button>
            </form>
            <p>
                <a href="{{ route('cooperation.auth.login', compact('cooperation')) }}">
                    @lang('auth.login.form.header')
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