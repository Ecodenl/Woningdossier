@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="w-full sm:w-1/2 xl:w-1/3 bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-2">
                {{ $cooperation->name }}
            </h1>
            <h2 class="heading-2">
                {{ config('app.name') }}
            </h2>

            @if(\Illuminate\Support\Facades\Auth::guest())
                <p>
                    <a class="btn btn-purple" href="{{ route('cooperation.auth.login') }}">
                        @lang('auth.login.form.header')
                    </a>
                    <br><br>
                    @lang('auth.login.no-account')
                    <a href="{{ CooperationSettingHelper::getSettingValue($cooperation, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                        @lang('auth.register.form.header')
                    </a>
                </p>
            @elseif(\Illuminate\Support\Facades\Auth::check())
                <div class="w-full flex flex-row flex-wrap justify-center">
                    <a class="btn btn-purple w-full xl:w-1/4 flex items-center justify-center mt-5"
                       href="{{ route('cooperation.home') }}">
                        @lang('default.start')
                        <i class="icon-sm icon-arrow-right-circle ml-5"></i>
                    </a>
                </div>
            @endif
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection
