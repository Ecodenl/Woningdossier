<div x-data="dropdown({{$initiallyOpen ?? false}})" x-ref="dropdown-wrapper" class="dropdown-wrapper w-inherit">
    <a href="#" x-on:click="toggle()" x-ref="dropdown-toggle" class="dropdown-toggle {{$class ?? ''}}">
        {!! $label !!}
    </a>
    <i x-show="open == false" class="icon-xs icon-arrow-down"></i>
    <i x-cloak x-show="open == true" class="icon-xs icon-arrow-up"></i>
    <ul x-cloak x-show="open" x-ref="dropdown" class="dropdown" x-on:click.outside="close()">
        {{ $slot }}
    </ul>
</div>