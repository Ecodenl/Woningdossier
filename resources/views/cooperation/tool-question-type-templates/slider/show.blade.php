@php
    $min = $toolQuestion->options['min'];
    $max = $toolQuestion->options['max'];
    $maxLabel = $toolQuestion->options['max_label'] ?? null;
    $maxLabel = is_null($maxLabel) ? $max : __($maxLabel, ['value' => $max]);

    $step = $toolQuestion->options['step'];
    $unit = $toolQuestion->unit_of_measure;

    $default = $max - $min;

    $livewireModel = "filledInAnswers.{$toolQuestion->short}";
@endphp
<div class="flex flex-wrap items-center w-full mt-12 slider-wrapper" x-data="slider(@entangle($livewireModel))" wire:ignore>
    <p class="w-1/12 flex justify-end pr-5">{{$min}}{!! $unit !!}</p>
    <div class="w-10/12 relative flex justify-center items-center">
        <input type="range" min="{{$min}}" max="{{$max}}" step="{{$step}}" class="slider" autocomplete="off"
               x-bind="slider" @if($disabled) disabled="disabled" @endif>
        <div class="slider-bubble" x-show="initialized" x-ref="slider-bubble" x-cloak>
            <span x-text="visual"></span>{!! $unit !!}
        </div>
    </div>
    <p class="w-1/12 flex justify-start pl-5">
        {{$maxLabel}}{!! $unit !!}
    </p>
</div>