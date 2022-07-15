@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="w-full sm:w-1/2 xl:w-1/3 bg-white rounded-3xl p-20 text-left space-y-4">
            {!! __('home.privacy.description', ['cooperation' => $cooperation->name]) !!}

            @if(\Illuminate\Support\Facades\Auth::guest())
                <p>
                    <a class="btn btn-purple" href="{{ route('cooperation.auth.login') }}">
                        @lang('auth.login.form.header')
                    </a>
                    <br><br>
                    @lang('auth.login.no-account')
                    <a href="{{ CooperationSettingHelper::getSettingValue($cooperation->id, CooperationSettingHelper::SHORT_REGISTER_URL, route('cooperation.register')) }}">
                        @lang('auth.register.form.header')
                    </a>
                </p>
            @elseif(\Illuminate\Support\Facades\Auth::check())
                <a class="btn btn-purple w-full xl:w-1/4 flex items-center justify-center mt-5"
                   href="{{ route('cooperation.home') }}">
                    @lang('default.start')
                    <i class="icon-sm icon-arrow-right-circle ml-5"></i>
                </a>
            @endif
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection
