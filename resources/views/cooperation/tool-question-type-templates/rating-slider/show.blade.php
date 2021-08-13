<div class="w-full grid grid-rows-3 grid-cols-2 grid-flow-row justify-items-center gap-x-32 lg:gap-x-64 gap-y-10">
    @foreach($toolQuestion->options as $option)
        @php
            $min = $option['min'];
            $max = $option['max'];

            $disabled = $disabled ?? false;
            $inputName = $option['name'];
            $label = $option['name'];

            $default = $filledInAnswers[$toolQuestion['id']][$option['short']] ?? 0;
            $componentName = "cooperation.frontend.tool.quick-scan.form";
            $livewireModel = "filledInAnswers.{$toolQuestion['id']}.{$option['short']}";
        @endphp


        <div x-data="ratingSlider({{$default ?? 0}}, '{{$activeClass ?? 'bg-green'}}', '{{$disabled}}')"
             x-ref="rating-slider-wrapper" class="w-inherit" wire:ignore>
            <input type="hidden" x-ref="rating-slider-input" data-short="{{ $option['short'] }}"
                   wire:model="{{$livewireModel}}"
                   x-on:element:updated.window="if ($event.detail.field === $el.getAttribute('wire:model')) { selectOptionByValue($event.detail.value);}">
            <div class="flex justify-between mb-3">
                <p>{{$label ?? ''}}</p>
                <p class="font-bold" x-text="value"></p>
            </div>

            {{-- We use a style tag because we can't be certain that Tailwind has the column class ready for us --}}
            <div class="w-full grid grid-rows-1 grid-flow-row gap-2" x-ref="rating-slider"
                 style="grid-template-columns: repeat({{ ($max - $min) + 1 }}, minmax(0, 1fr));">
                @for($i = $min; $i <= $max; $i++)
                    <div class="w-full h-2 bg-gray @if($disabled) cursor-not-allowed @else cursor-pointer @endif"
                         data-value="{{$i}}" x-on:mouseenter="mouseEnter($el)" x-on:mouseleave="mouseLeave($el)"
                         x-on:click="selectOptionByElement($el)">

                    </div>
                @endfor
            </div>
        </div>
    @endforeach
</div>