@extends('cooperation.frontend.layouts.tool')

@section('content')

    @php
        // this session status is returned after the user succesfully confirmed its 2FA
        $showRecoveryCodes = session('status') == 'two-factor-authentication-confirmed';
    @endphp
    <div class="w-full flex flex-row flex-wrap" x-data="{showRecoveryCodes: '{{$showRecoveryCodes}}'}">
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

                @if (session('status') !== 'two-factor-authentication-enabled')
                    @php
                        $state = 'inactive';
                        if ($account->hasEnabledTwoFactorAuthentication()) {
                            $state = 'active';
                        }
                    @endphp
                    <div class="w-full flex justify-center items-center my-5">
                        <div class="w-3/5 bg-blue-100 rounded-lg shadow-sm p-5 border-dashed border border-purple flex flex-col sm:flex-row justify-between items-center gap-2 sm:gap-0 ">
                            <div class="flex flex-col sm:flex-row justify-start items-center gap-4">
                                @if ($account->hasEnabledTwoFactorAuthentication())
                                <div class="bg-green bg-opacity-25 flex p-2 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                @else
                                    <div class="bg-red bg-opacity-25 flex p-2 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                                    </svg>
                                    </div>

                                @endif
                                <div class="font-semibold text-blue text-md">
                                    <h1 class="heading-3">
                                        @lang("my-account.2fa.index.alert.{$state}.title")
                                    </h1>
                                    <p class="font-semibold text-blue text-sm">
                                        @lang("my-account.2fa.index.alert.{$state}.text")
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                @if($account->hasEnabledTwoFactorAuthentication())
                                    <form action="{{url('/user/two-factor-authentication')}}" method="post">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-purple" type="submit">
                                            @lang("my-account.2fa.index.alert.active.button")
                                        </button>
                                    </form>
                                    <button x-text="showRecoveryCodes ? '@lang("my-account.2fa.index.recovery-codes.hide")' : '@lang("my-account.2fa.index.recovery-codes.show")'"
                                            x-on:click="showRecoveryCodes = !showRecoveryCodes" class="btn btn-purple"
                                            type="submit">

                                    </button>
                                @else
                                    <form action="{{url('/user/two-factor-authentication')}}" method="post">
                                        @csrf
                                        <button class="btn btn-purple" type="submit">
                                            @lang("my-account.2fa.index.alert.inactive.button")
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>


                    @if($account->hasEnabledTwoFactorAuthentication())
                        <div class="w-full flex flex-col justify-center my-5" x-show="showRecoveryCodes">
                            @if (session('status') == 'two-factor-authentication-confirmed')
                            <h5 class="heading-5 text-center">
                                @lang('my-account.2fa.index.confirmed.enabled')
                            </h5>
                            @endif
                            <h5 class="heading-6 text-center">
                                @lang('my-account.2fa.index.recovery-codes.label')
                            </h5>

                            @include('cooperation.my-account.two-factor-authentication.recovery-code')
                        </div>
                    @endif

                @endif


                @if (session('status') == 'two-factor-authentication-enabled')
                    <div class="grid grid-flow-row auto-rows-max w-full place-items-center gap-y-4 my-2">
                        <h5 class="heading-5">
                            @lang('my-account.2fa.index.setup.scan')
                        </h5>
                        <div class="justify-center  flex w-full">
                            {!! $account->twoFactorQrCodeSvg();  !!}
                        </div>
                        <div class="justify-center  flex w-full">
                            <form action="{{url('/user/confirmed-two-factor-authentication ')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <div class="form-header">
                                        <label for="" class="form-label">@lang('general.2fa-input')</label>
                                    </div>
                                    <div class="input-group ">
                                        <input class="form-input" name="code" placeholder="" type="text">
                                    </div>
                                </div>
                                <button class="w-full btn btn-green" type="submit">
                                    @lang("general.confirm")
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

