@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="w-full sm:w-1/2 xl:w-1/3 bg-white rounded-3xl p-20 text-center space-y-10">
            {!! __('home.disclaimer.description.title') !!}

            <p>
                <a class="btn btn-purple" href="{{ route('cooperation.auth.login') }}">
                    @lang('auth.login.form.header')
                </a>
                <br><br>
                @lang('auth.login.no-account')
                <a href="{{ route('cooperation.register') }}">
                    @lang('auth.register.form.header')
                </a>
            </p>
        </div>
        <div class="mt-5 text-center">
            <a href="{{ route('cooperation.privacy.index') }}" class="text-white">
                @lang('default.privacy-policy')
            </a>
            <span class="text-white">|</span>
            <a href="{{ route('cooperation.disclaimer.index') }}" class="text-white">
                @lang('default.terms-and-conditions')
            </a>
        </div>
    </div>
@endsection
