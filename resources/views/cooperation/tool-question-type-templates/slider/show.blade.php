@php
    $min = $toolQuestion->options['min'];
    $max = $toolQuestion->options['max'];

    $step = $toolQuestion->options['step'];
    $unit = $toolQuestion->unit_of_measure;

    $default = $max - $min;
@endphp
<div class="flex flex-wrap items-center w-full mt-12" x-data="slider()" wire:ignore>
    <p class="w-1/12 flex justify-end pr-5">{{$min}}{!! $unit !!}</p>
    <div class="w-10/12 relative flex justify-center items-center">
        <input type="range" min="{{$min}}" max="{{$max}}" step="{{$step}}" class="slider" autocomplete="off"
               wire:model.lazy="filledInAnswers.{{$toolQuestion->id}}"
               x-ref="slider" x-on:input="updateVisuals()"
               x-on:livewire:load.window="updateVisuals({{ $filledInAnswers[$toolQuestion->id] ?? $default }})"
               x-on:element:updated.window="if ($event.detail.field === $el.getAttribute('wire:model')) {updateVisuals();}">
        <div class="slider-bubble" x-show="initialized" x-ref="slider-bubble">
            <span x-text="value"></span>{!! $unit !!}
        </div>
    </div>
    <p class="w-1/12 flex justify-start pl-5">
        {{$max}}{!! $unit !!}
    </p>
</div>