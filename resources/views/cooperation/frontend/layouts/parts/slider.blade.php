@php
    $min = $min ?? 0;
    $max = $max ?? 10;
    // Set proper values
    $min = $min < 0 ? 0 : $min;
    $max = $max <= $min ? $min + 10 : $max;

    $step = $step ?? 1;
    $unit = $unit ?? '';
@endphp
<div class="flex flex-wrap items-center w-full mt-12 slider-wrapper" x-data="slider()">
    <p class="w-1/12 flex justify-end pr-5">{{$min}}{!! $unit !!}</p>
    <div class="w-10/12 relative flex justify-center items-center">
        <input type="range" min="{{$min}}" max="{{$max}}" step="{{$step}}" name="{{$inputName ?? ''}}" class="slider"
               x-ref="slider" x-on:input="updateVisuals()" x-model="value">
        <div class="slider-bubble" x-show="initialized" x-ref="slider-bubble">
            <span x-text="value"></span>{!! $unit !!}
        </div>
    </div>
    <p class="w-1/12 flex justify-start pl-5" x-on:click="document.querySelector('.slider').value = 4;">{{$max}}{!! $unit !!}</p>
</div>
