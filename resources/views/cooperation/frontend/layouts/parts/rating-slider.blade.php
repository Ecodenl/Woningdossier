@php
    $min = $min ?? 1;
    $max = $max ?? 5;
    // Set proper values
    $min = $min < 1 ? 1 : $min;
    $max = $max <= $min ? $min + 5 : $max;

    $disabled = $disabled ?? false;

    $livewire ??= false;
    $inputName ??= 'rating-slider';
    // TODO: Make proper conversion, not relevant ATM
    $htmlName = $inputName;

    $default ??= 0;
@endphp
<div x-data="ratingSlider(@if($livewire) @entangle($inputName) @else {{$default}} @endif, '{{$activeClass ?? 'bg-green'}}', {{$disabled}})"
     x-ref="rating-slider-wrapper" class="rating-slider-wrapper w-inherit @error($inputName) form-error @enderror">
    <input type="hidden" x-bind="input" name="{{$htmlName}}">
    <div class="flex justify-between mb-3">
        <p class="@error($inputName) text-red @enderror">{{$label ?? ''}}</p>
        <p class="font-bold" wire:ignore x-text="value"></p>
    </div>

    {{-- We use a style tag because we can't be certain that Tailwind has the column class ready for us --}}
    <div class="w-full grid grid-rows-1 grid-flow-row gap-2" x-ref="rating-slider" wire:ignore
         style="grid-template-columns: repeat({{ ($max - $min) + 1 }}, minmax(0, 1fr));">
        @for($i = $min; $i <= $max; $i++)
            <div class="w-full h-2 bg-gray @if($disabled) cursor-not-allowed @else cursor-pointer @endif"
                 data-value="{{$i}}" x-bind="block">

            </div>
        @endfor
    </div>

    @error($inputName)
    <p class="form-error-label">
        {{ $message }}
    </p>
    @enderror
</div>