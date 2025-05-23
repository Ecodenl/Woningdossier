<div class="w-full grid grid-rows-3 grid-cols-2 grid-flow-row justify-items-center gap-x-32 lg:gap-x-64 gap-y-8">
    @foreach($toolQuestion->options as $option)
        @php
            $disabled ??= false;
            $livewireModel = "filledInAnswers.{$toolQuestion->short}.{$option['short']}";
        @endphp

        @include('cooperation.frontend.layouts.parts.rating-slider', [
            'inputShort' => $option['short'],
            'inputName' => $livewireModel,
            'livewire' => true,
            'min' => $option['min'],
            'max' => $option['max'],
            'disabled' => $disabled,
            'label' => $option['name'],
            'attr' => "wire:key=\"rating-slider-{$toolQuestion->short}-{$option['short']}\"",
        ])
    @endforeach
</div>