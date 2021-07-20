@php
    $min = $min ?? 0;
    $max = $max ?? 10;
    // Set proper values
    $min = $min < 0 ? 0 : $min;
    $max = $max <= $min ? $min + 10 : $max;

    $step = $step ?? ($max / 10);
    $unit = $unit ?? '';
@endphp
<div class="flex flex-wrap items-center w-full mt-12">
        <div class="slider-bubble">{{$step}}</div>
    <p class="w-1/12 flex justify-end pr-5">{{$min}}{!! $unit !!}</p>
    <input type="range" min="{{$min}}" max="{{$max}}" step="{{$step}}" name="{{$inputName ?? ''}}" class="slider">
    <p class="w-1/12 flex justify-start pl-5">{{$max}}{!! $unit !!}</p>
</div>
