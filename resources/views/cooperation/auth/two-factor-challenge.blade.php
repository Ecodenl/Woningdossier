@extends('cooperation.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.login.form.header')
            </h1>
            @if(session('verified'))
                @component('cooperation.layouts.components.alert', ['color' => 'blue-900'])
                    @lang('cooperation/auth/verify.success-log-in')
                @endcomponent
            @endif
            @if(session('success'))
                @component('cooperation.layouts.components.alert', ['color' => 'blue-900'])
                    {{session('success')}}
                @endcomponent
            @endif
            @if(session('status'))
                @component('cooperation.layouts.components.alert', ['color' => 'green'])
                    {{ session('status') }}
                @endcomponent
            @endif
            @if($errors->has('cooperation'))
                @component('cooperation.layouts.components.alert', ['color' => 'red'])
                    @foreach($errors->get('cooperation') as $message)
                        {{ $message }}
                    @endforeach
                @endcomponent
            @endif

            @php
                $withCode = true;
                $withRecoveryCode = false;
                if($errors->has('recovery_code')) {
                    $withRecoveryCode = true;
                    $withCode = false;
                }
                if ($errors->has('code')) {
                    $withRecoveryCode = false;
                    $withCode = true;
                }
            @endphp
            <div class="grid grid-flow-row auto-rows-max w-full place-items-center gap-y-4 my-2">
                <div class="flex w-full" x-data="{withCode: '{{$withCode}}', withRecoveryCode: '{{$withRecoveryCode}}'}">
                    <form action="{{route('cooperation.auth.two-factor.challenge')}}" method="post">
                        @csrf
                        <div x-show="withCode">
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'class' => 'w-full',
                                'inputName' => 'code',
                                'id' => 'code',
                                'label' => __('general.2fa-input')
                               ])
                                <input class="form-input" name="code" type="text" placeholder="@lang('general.2fa-input')">
                            @endcomponent
                        </div>
                        <div x-show="withRecoveryCode">
                            @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'class' => 'w-full',
                                    'inputName' => 'recovery_code',
                                    'id' => 'recovery_code',
                                    'label' => __('auth.two-factor-challenge.recovery-code-label')
                                ])
                                <input class="form-input" name="recovery_code" type="text" placeholder="@lang('general.2fa-recovery-code')">
                            @endcomponent
                        </div>
                        <button class="w-full btn btn-purple" type="submit">
                            @lang("general.confirm")
                        </button>

                        <button class="w-full btn btn-orange mt-3" type="button" x-on:click="withRecoveryCode = true; withCode = false;" x-show="withRecoveryCode == false">
                            Ik wil mijn herstelcode gebruiken
                        </button>

                        <button class="w-full btn btn-green mt-3" type="button" x-on:click="withRecoveryCode = false; withCode = true;" x-show="withRecoveryCode == true">
                            Ik wil toch mijn 2FA code gebruiken
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection