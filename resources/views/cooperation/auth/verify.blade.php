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

                        @lang('cooperation/auth/verify.body', ['link' => route('cooperation.auth.verification.resend')])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
