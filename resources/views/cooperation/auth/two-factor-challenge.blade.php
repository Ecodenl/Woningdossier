@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.login.form.header')
            </h1>
            @if(session('verified'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                    @lang('cooperation/auth/verify.success-log-in')
                @endcomponent
            @endif
            @if(session('success'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                    {{session('success')}}
                @endcomponent
            @endif
            @if(session('status'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green'])
                    {{ session('status') }}
                @endcomponent
            @endif
            @if($errors->has('cooperation'))
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'red'])
                    @foreach($errors->get('cooperation') as $message)
                        {{ $message }}
                    @endforeach
                @endcomponent
            @endif
            {{dd($errors)}}
            <div class="grid grid-flow-row auto-rows-max w-full place-items-center gap-y-4 my-2">
                <div class="flex w-full" x-data="{withCode: true, withRecoveryCode: false}">
                    <form action="{{url('/two-factor-challenge ')}}" method="post">
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
                                    'inputName' => 'recover_code',
                                    'id' => 'recovery_code',
                                    'label' => __('auth.two-factor-challenge.recovery-code-label')
                                ])
                                <input class="form-input" name="recovery_code" type="text" placeholder="@lang('general.2fa-recovery-code')">
                            @endcomponent
                        </div>
                        <button class="w-full btn btn-purple" type="submit">
                            @lang("general.confirm")
                        </button>

                        <button class="w-full btn btn-orange mt-3" type="button" x-on:click="withRecoveryCode = true; withCode = false;" x-show="withRecoveryCode === false">
                            Ik wil mijn herstelcode gebruiken
                        </button>

                        <button class="w-full btn btn-green mt-3" type="button" x-on:click="withRecoveryCode = false; withCode = true;" x-show="withRecoveryCode === true">
                            Ik wil toch mijn 2FA code gebruiken
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection