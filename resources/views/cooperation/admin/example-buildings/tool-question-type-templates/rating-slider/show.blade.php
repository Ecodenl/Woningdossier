<div class="w-full grid grid-rows-3 grid-cols-2 grid-flow-row justify-items-center gap-x-32 lg:gap-x-64 gap-y-8">
    @foreach($toolQuestion->options as $option)
        @php
            $min = $option['min'];
            $max = $option['max'];

            $disabled = $disabled ?? false;
            $label = $option['name'];

            $default = $filledInAnswers[$toolQuestion['short']][$option['short']] ?? 0;
            $livewireModel = "filledInAnswers.{$toolQuestion['short']}.{$option['short']}";
        @endphp

        <div x-data="ratingSlider({{$default ?? 0}}, '{{$activeClass ?? 'bg-green'}}', '{{$disabled}}')"
             x-ref="rating-slider-wrapper" class="rating-slider-wrapper w-inherit @error($livewireModel) form-error @enderror">
            <input type="hidden" x-ref="rating-slider-input" data-short="{{ $option['short'] }}"
                   wire:model="{{$livewireModel}}"
                   x-on:element:updated.window="if ($event.detail.field === $el.getAttribute('wire:model')) { selectOptionByValue($event.detail.value);}">
            <div class="flex justify-between mb-3">
                <p class="@error($livewireModel) text-red @enderror">{{$label ?? ''}}</p>
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

            @error($livewireModel)
            <p class="form-error-label">
                {{ $message }}
            </p>
            @enderror
        </div>
    @endforeach
</div>