<div x-data="dropdown(!! '{{$initiallyOpen ?? false}}')" x-ref="dropdown-wrapper"
     class="dropdown-wrapper w-inherit inline-flex items-center">
    <a href="#" x-on:click="toggle()" x-ref="dropdown-toggle" class="dropdown-toggle {{$class ?? ''}}"
       @if(! empty($dropdownTitle)) title="{{ $dropdownTitle }}" @endif>
        {!! $label !!}
        <i x-show="open == false" class="ml-1 icon-xs icon-arrow-down cursor-pointer select-none" x-on:click="toggle()"></i>
        <i x-cloak x-show="open == true" class="ml-1 icon-xs icon-arrow-up cursor-pointer select-none" x-on:click="toggle()"></i>
    </a>
    <ul x-cloak x-show="open" x-ref="dropdown" class="dropdown" x-on:click.outside="close()">
        {{ $slot }}
    </ul>
</div>