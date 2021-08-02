<div class="w-full grid grid-rows-3 grid-cols-2 grid-flow-row justify-items-center gap-x-32 lg:gap-x-64 gap-y-10">
{{--    @foreach($toolQuestion as $toolQuestionValue)--}}
        @php
            $min = $toolQuestion->options['min'];
            $max = $toolQuestion->options['max'];

            $disabled = $disabled ?? false;
            $inputName = $toolQuestion->name;
        @endphp

        <div x-data="ratingSlider({{$default ?? 0}}, '{{$activeClass ?? 'bg-green'}}', {{$disabled}})"
             x-ref="rating-slider-wrapper" class="w-inherit" wire:ignore>
            <input class="hidden" name="{{$inputName ?? ''}}" x-ref="rating-slider-input" x-model="value">
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
                         x-on:click="selectOption($el)">

                    </div>
                @endfor
            </div>
        </div>
{{--    @endforeach--}}
</div>