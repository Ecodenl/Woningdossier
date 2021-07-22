<div class="w-full relative z-100 flex flex-wrap justify-center {{$class ?? ''}}">
    @if(session('success'))
        @component('cooperation.frontend.layouts.parts.alert', ['class' => 'border-green'])
            {{ session('success') }}
        @endcomponent
    @endif
    @if(session('warning'))
        @component('cooperation.frontend.layouts.parts.alert', ['class' => 'border-yellow'])
            {{ session('warning') }}
        @endcomponent
    @endif
    @if($errors->any())
        @component('cooperation.frontend.layouts.parts.alert', ['class' => 'border-red'])
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        @endcomponent
    @endif
    @if(session('verified'))
        @component('cooperation.frontend.layouts.parts.alert', ['class' => 'border-blue-800'])
            @lang('cooperation/auth/verify.success-log-in')
        @endcomponent
    @endif
</div>