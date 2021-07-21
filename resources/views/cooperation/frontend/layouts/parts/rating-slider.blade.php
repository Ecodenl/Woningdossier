@php
    $min = $min ?? 1;
    $max = $max ?? 5;
    // Set proper values
    $min = $min < 1 ? 1 : $min;
    $max = $max <= $min ? $min + 5 : $max;
@endphp
<div x-data="ratingSlider({{$min}}, {{$max}})" x-ref="rating-slider-wrapper" class="w-inherit">
    <input class="hidden" name="{{$inputName ?? ''}}" x-ref="rating-slider-input" x-model="value">
    <div class="flex justify-between mb-3">
        <p>{{$label ?? ''}}</p>
        <p class="font-bold" x-text="value"></p>
    </div>

    {{-- We use a style tag because we can't be certain that Tailwind has the column class ready for us --}}
    <div class="w-full grid grid-rows-1 grid-flow-row gap-4" x-ref="rating-slider"
         style="grid-template-columns: repeat({{ ($max - $min) + 1 }}, minmax(0, 1fr));">
        @for($i = $min; $i <= $max; $i++)
            <div class="w-full h-2 bg-gray" data-value="{{$i}}" x-on:mouseenter="mouseEnter($el)"
                 x-on:mouseleave="mouseLeave($el)" x-on:click="selectOption($el)">

            </div>
        @endfor
    </div>
</div>