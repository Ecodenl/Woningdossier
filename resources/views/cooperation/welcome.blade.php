@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="w-1/3 bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                {{ $cooperation->name }}
            </h1>
            <h2 class="heading-2">
                {{ config('app.name') }}
            </h2>

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
