{{-- TODO: Move to cooperation/layouts --}}
@php $initiallyOpen ??= false; $withSearch ??= false; @endphp
<div x-data="alpineSelect(@js($initiallyOpen), @js($withSearch))" x-ref="select-wrapper" class="select-wrapper"
     x-on:keyup.escape.window="close()">
    {{-- Expect at least a select with options --}}
    {{ $slot }}

    <div class="input-group" x-ref="select-input-group" x-cloak wire:ignore>
        @if(! empty(($prepend ?? null)))
            <div class="input-group-prepend">
                {!! $prepend !!}
            </div>
        @endif
        <i class="select-icon" data-icon="{{$icon ?? ''}}" x-ref="select-icon"></i>
        <input class="form-input @if(! empty(($append ?? null))) with-append @endif" x-ref="select-input"
               x-model="search" x-on:click="toggle()" x-bind:readonly="! withSearch" x-on:input="open = true">
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