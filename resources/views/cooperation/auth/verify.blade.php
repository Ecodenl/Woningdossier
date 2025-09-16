@extends('cooperation.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('cooperation/auth/verify.heading')
            </h1>
            @if (session('resent'))
                @component('cooperation.layouts.components.alert', ['color' => 'green'])
                    @lang('cooperation/auth/verify.resent')
                @endcomponent
            @endif
            @if(session('status'))
                @component('cooperation.layouts.components.alert', ['color' => 'green'])
                    @lang(session('status'))
                @endcomponent
            @endif
            <div class="as-text">
                @lang('cooperation/auth/verify.body')
                <form method="POST" id="resend-form" class="inline"
                      action="{{ route('cooperation.auth.verification.send') }}">
                    @csrf
                    <button class="as-link">
                        @lang('cooperation/auth/verify.do-it')
                    </button>
                </form>
            </div>
            <button class="btn btn-outline-purple" id="reload-page">
                @lang('cooperation/auth/verify.reload-page')
            </button>

            <p>
                @if(\Illuminate\Support\Facades\Auth::check())
                    @include('cooperation.layouts.parts.logout')
                @else
                    <a href="{{ route('cooperation.auth.login') }}">
                        @lang('auth.login.form.header')
                    </a>
                    <br><br>
                    @lang('auth.login.no-account')
                    <a href="{{ CooperationSettingHelper::getSettingValue($cooperation, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                        @lang('auth.register.form.header')
                    </a>
                @endif
            </p>
        </div>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function(e) {
            document.getElementById('reload-page').addEventListener('click', () => location.reload());
        });
    </script>
@endpush