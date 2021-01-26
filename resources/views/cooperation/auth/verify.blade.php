@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('cooperation/auth/verify.heading')</div>

                    <div class="panel-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                @lang('cooperation/auth/verify.resent')
                            </div>
                        @endif

                        @lang('cooperation/auth/verify.body')
                            <form id="resend-form" method="POST" action="{{ route('cooperation.auth.verification.resend') }}" style="display: inline">
                                @csrf

                                <a onclick="document.getElementById('resend-form').submit()">{{ __('cooperation/auth/verify.do-it') }}</a>
                            </form>
                        @lang('cooperation/auth/verify.already-verified')
                        <br>
                        <button class="btn btn-default" onclick="location.reload()">@lang('cooperation/auth/verify.reload-page')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
