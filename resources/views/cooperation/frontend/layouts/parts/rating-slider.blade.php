@php
    $min = $min ?? 1;
    $max = $max ?? 5;
    // Set proper values
    $min = $min < 1 ? 1 : $min;
    $max = $max <= $min ? $min + 5 : $max;

    $disabled = $disabled ?? false;
@endphp
<div x-data="ratingSlider({{$default ?? 0}}, '{{$activeClass ?? 'bg-green'}}', {{$disabled}})"
     x-ref="rating-slider-wrapper" class="rating-slider-wrapper w-inherit">
    <input type="hidden" x-ref="rating-slider-input"
           @if(($livewire ?? false)) wire:model="{{$inputName ?? ''}}" @else name="{{$inputName ?? ''}}" @endif
           @if(($livewire ?? false)) x-on:element:updated.window="if ($event.detail.field === $el.getAttribute('wire:model')) { selectOptionByValue($event.detail.value);}" @endif>
    <div class="flex justify-between mb-3">
        <p>{{$label ?? ''}}</p>
        <p class="font-bold" wire:ignore x-text="value"></p>
    </div>

    {{-- We use a style tag because we can't be certain that Tailwind has the column class ready for us --}}
    <div class="w-full grid grid-rows-1 grid-flow-row gap-2" x-ref="rating-slider" wire:ignore
         style="grid-template-columns: repeat({{ ($max - $min) + 1 }}, minmax(0, 1fr));">
        @for($i = $min; $i <= $max; $i++)
            <div class="w-full h-2 bg-gray @if($disabled) cursor-not-allowed @else cursor-pointer @endif"
                 data-value="{{$i}}" x-on:mouseenter="mouseEnter($el)" x-on:mouseleave="mouseLeave($el)"
                 x-on:click="selectOptionByElement($el)">

            </div>
        @endfor
    </div>
</div>