@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-full min-h-screen flex justify-center items-center flex-col py-20">
        <div class="bg-white rounded-3xl p-20 text-center space-y-10">
            @include('cooperation.frontend.layouts.parts.logo')
            <h1 class="heading-1">
                @lang('auth.confirm-password.title')
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


            <div class="grid grid-flow-row auto-rows-max w-full place-items-center gap-y-4 my-2">
                <div class="flex w-full">
                    <form action="{{route('cooperation.auth.password.confirm')}}" method="post">
                        @csrf
                        <div>
                            @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'class' => 'w-full',
                                    'inputName' => 'password',
                                    'id' => 'password',
                                    'label' => __('auth.confirm-password.password.title')
                                ])
                                <input class="form-input" name="password" type="text" placeholder="@lang('auth.confirm-password.password.placeholder')">
                            @endcomponent
                        </div>
                        <button class="w-full btn btn-purple" type="submit">
                            @lang("general.confirm")
                        </button>

                    </form>
                </div>
            </div>
        </div>
        @include('cooperation.frontend.shared.parts.privacy-disclaimer')
    </div>
@endsection