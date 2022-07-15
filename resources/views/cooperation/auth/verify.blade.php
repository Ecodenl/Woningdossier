@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('cooperation/auth/verify.heading')
            </h1>
            @if (session('resent'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    @lang('cooperation/auth/verify.resent')
                @endcomponent
            @endif
            @if(session('status'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('status') }}
                @endcomponent
            @endif
            <p>
                @lang('cooperation/auth/verify.body')
                <a onclick="document.getElementById('resend-form').submit()">
                    @lang('cooperation/auth/verify.do-it')
                </a>
            </p>
            <button class="btn btn-outline-purple" onclick="location.reload()">
                @lang('cooperation/auth/verify.reload-page')
            </button>

            <p>
                @if(\Illuminate\Support\Facades\Auth::check())
                    @include('cooperation.frontend.shared.parts.logout')
                @else
                    <a href="{{ route('cooperation.auth.login') }}">
                        @lang('auth.login.form.header')
                    </a>
                    <br><br>
                    @lang('auth.login.no-account')
                    <a href="{{ CooperationSettingHelper::getSettingValue($cooperation->id, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                        @lang('auth.register.form.header')
                    </a>
                @endif
            </p>
        </div>
    </div>
    <form method="POST" class="hidden" id="resend-form"
          action="{{ route('cooperation.auth.verification.resend') }}">
        @csrf
    </form>
@endsection