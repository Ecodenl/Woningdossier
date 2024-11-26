@extends('cooperation.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-10 md:py-20"
         x-data="register('{{route('cooperation.check-existing-email')}}')">
        <div class="bg-white rounded-3xl p-4 md:p-20 text-center space-y-6 md:space-y-10 w-3/4">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.register.form.header')
            </h1>
            @if(session('success'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('success') }}
                @endcomponent
            @endif
            <form class="w-full flex flex-wrap justify-center" method="POST" id="register"
                  action="{{ route('cooperation.register') }}">
                @csrf
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full',
                    'inputName' => 'email',
                ])
                    <input class="form-input" type="text" name="email" value="{{ old('email') }}"
                           placeholder="@lang('auth.register.form.email')" x-on:change="checkEmail($el)">
                    <p class="text-red w-full text-left" x-show="showEmailWarning" x-cloak>
                        @lang('auth.register.form.possible-wrong-email')
                    </p>
                    <p class="text-blue-800 w-full text-left" x-show="alreadyMember" x-cloak>
                        @lang('auth.register.form.already-member')
                    </p>
                    <p class="text-blue-800 w-full text-left" x-show="emailExists" x-cloak>
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

                @include('cooperation.layouts.address', [
                    'attr' => 'x-show="! alreadyMember"',
                    'withLabels' => false,
                ])

                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'phone_number',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <input class="form-input" type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="@lang('auth.register.form.phone-number')">
                @endcomponent
                <div class="flex w-full flex-col" x-show="! alreadyMember && ! emailExists">
                    <div class="flex justify-start">
                        <span class="text-green text-sm">@lang('validation.custom.password.min')</span>
                    </div>
                    <div class="flex flex-col lg:flex-row">
                        @component('cooperation.frontend.layouts.components.form-group', [
                             'withInputSource' => false,
                             'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                             'inputName' => 'password',
                        ])

                            <div class="flex w-full" x-data="{showPass: false}">
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
                    </div>
                </div>
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'class' => 'w-full -mt-5',
                    'inputName' => 'allow_access',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                    <div class="checkbox-wrapper mb-1">
                        <input id="allow-access" name="allow_access" type="checkbox" value="1" x-model="allowAccess">
                        <label for="allow-access">
                            <span class="checkmark"></span>
                            <span>
                                @lang('conversation-requests.index.form.allow-access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])
                            </span>
                        </label>
                    </div>
                    <p class="text-left">@lang('conversation-requests.index.text', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])</p>
                @endcomponent

                {{-- When clicking the button, we disable it. We don't have to do anything fancy, since it won't have pointer events when disabled --}}
                <button class="btn btn-purple w-full mt-3" type="submit" x-on:click="setTimeout(() => {submitted = true;});"
                        x-bind:disabled="! allowAccess || alreadyMember || submitted">
                    @lang('auth.register.form.submit')
                </button>
            </form>
            <p>
                <a href="{{ route('cooperation.auth.login') }}">
                    @lang('auth.login.form.header')
                </a>
            </p>
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection