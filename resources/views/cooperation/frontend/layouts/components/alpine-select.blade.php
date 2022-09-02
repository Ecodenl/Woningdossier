<div x-data="alpineSelect(!!'{{$initiallyOpen ?? false}}')" x-ref="select-wrapper" class="select-wrapper">
    {{-- Expect at least a select with options --}}
    {{ $slot }}

    <div class="input-group" x-ref="select-input-group" x-cloak wire:ignore>
        @if(! empty(($prepend ?? null)))
            <div class="input-group-prepend">
                {!! $prepend !!}
            </div>
        @endif
        @if(! empty(($icon ?? null)))
            <i class="select-icon {{$icon}}"></i>
        @endif
        <input class="form-input @if(! empty(($append ?? null))) with-append @endif" readonly x-ref="select-input"
               x-on:click="toggle()">
        @if(! empty(($append ?? null)))
            <div class="input-group-append">
                {!! $append !!}
            </div>
        @endif
        <i x-show="open == false" class="icon-sm icon-arrow-down"></i>
        <i x-show="open == true" class="icon-sm icon-arrow-up"></i>
    </div>

    <div x-cloak x-ref="select-options" class="select-dropdown" x-show="open" x-on:click.outside="close()" wire:ignore>
        <!-- Will be populated by Alpine -->
    </div>
</div>