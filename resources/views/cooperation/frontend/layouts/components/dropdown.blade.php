<div x-data="dropdown({{$initiallyOpen ?? false}})" x-ref="dropdown-wrapper" class="dropdown-wrapper w-inherit">
    <a href="#" x-on:click="toggle()" x-ref="dropdown-toggle" class="dropdown-toggle select-none {{$class ?? ''}}">
        {!! $label !!}
    </a>
    <i x-show="open == false" class="icon-xs icon-arrow-down cursor-pointer select-none" x-on:click="toggle()"></i>
    <i x-cloak x-show="open == true" class="icon-xs icon-arrow-up cursor-pointer select-none" x-on:click="toggle()"></i>
    <ul x-cloak x-show="open" x-ref="dropdown" class="dropdown" x-on:click.outside="close()">
        {{ $slot }}
    </ul>
</div>