@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full flex flex-row flex-wrap">
        <div class="w-full space-y-10">
            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-lg">
                    @lang('my-account.index.header')
                </div>
            </div>


            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                    @lang('my-account.2fa.index.title')
                </div>

                @php
                $state = 'inactive';
                @endphp
                <div class="w-full flex justify-center items-center my-5">
                    <div class="w-3/5 bg-blue-100 rounded-lg shadow-sm p-5 border-dashed border border-purple flex flex-col sm:flex-row justify-between items-center gap-2 sm:gap-0 ">
                        <div class="flex flex-col sm:flex-row justify-start items-center gap-4">
                            <div class="bg-green bg-opacity-25 flex p-2 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="font-semibold text-blue text-md">
                                <h1 class="heading-3">
                                    @lang("my-account.2fa.index.alert.{$state}.title")
                                </h1>
                                <p class="font-semibold text-blue text-sm">
                                    @lang("my-account.2fa.index.alert.{$state}.text")
                                </p>
                            </div>
                        </div>
                        <div>
                            <form action="{{url('/user/two-factor-authentication')}}" method="post">
                                @csrf
                                <button class="btn btn-purple" type="submit">
                                    @lang("my-account.2fa.index.alert.{$state}.button")
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if (session('status') == 'two-factor-authentication-enabled')
                    <div class="mb-4 font-medium text-sm">
                        Please finish configuring two factor authentication below.
                    </div>

                    {!! request()->user()->twoFactorQrCodeSvg();  !!}


                    <form action="{{url('/user/confirmed-two-factor-authentication ')}}" method="post">
                        @csrf
                        <div class="input-group ">
                            <input class="form-input" name="code" placeholder="" type="text">
                        </div>
                        <button type="submit"></button>
                    </form>
                @endif

                @if (session('status') == 'two-factor-authentication-confirmed')
                    <div class="mb-4 font-medium text-sm">
                        Two factor authentication confirmed and enabled successfully.
                    </div>
                @endif

                {{implode('-', request()->user()->recoveryCodes())}}

            </div>


        </div>
    </div>
@endsection

