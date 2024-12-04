<div class="w-full relative z-100 flex flex-wrap justify-center {{$class ?? ''}}">
    @if(session('success'))
        @component('cooperation.layouts.components.alert', ['color' => 'green'])
            {{ session('success') }}
        @endcomponent
    @endif
    @if(session('warning'))
        @component('cooperation.layouts.components.alert', ['color' => 'yellow'])
            {{ session('warning') }}
        @endcomponent
    @endif
    @if($errors->any())
        @component('cooperation.layouts.components.alert', ['color' => 'red'])
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        @endcomponent
    @endif
</div>