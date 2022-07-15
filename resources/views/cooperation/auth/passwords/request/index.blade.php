@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.email.form.header')
            </h1>
            @if(session('status'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('status') }}
                @endcomponent
            @endif
            <form class="w-full flex flex-wrap justify-center" method="POST"
                  action="{{ route('cooperation.auth.password.request.store') }}">
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
                <button class="btn btn-purple w-full mt-6">
                    @lang('auth.email.form.send-reset-link')
                </button>
            </form>
            <p>
                <a href="{{ route('cooperation.auth.login') }}">
                    @lang('auth.login.form.header')
                </a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="{{ CooperationSettingHelper::getSettingValue($cooperation->id, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                    @lang('auth.register.form.header')
                </a>
            </p>
        </div>
    </div>
@endsection