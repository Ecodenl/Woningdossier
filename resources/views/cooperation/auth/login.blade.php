@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.login.form.header')
            </h1>
            @if(session('verified'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                    @lang('cooperation/auth/verify.success-log-in')
                @endcomponent
            @endif
            @if(session('success'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                    {{session('success')}}
                @endcomponent
            @endif
            @if(session('status'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('status') }}
                @endcomponent
            @endif
            @if($errors->has('cooperation'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'red'])
                    @foreach($errors->get('cooperation') as $message)
                        {{ $message }}
                    @endforeach
                @endcomponent
            @endif
            <form class="w-full flex flex-wrap justify-center" method="POST"
                  action="{{ route('cooperation.auth.login') }}">
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
                    <div class="flex w-full" x-data="{showPass: false}">
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
                <a href="{{ route('cooperation.auth.password.request.index') }}">
                    @lang('auth.login.form.forgot-password')
                </a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="{{ CooperationSettingHelper::getSettingValue($cooperation->id, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                    @lang('auth.register.form.header')
                </a>
            </p>
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection