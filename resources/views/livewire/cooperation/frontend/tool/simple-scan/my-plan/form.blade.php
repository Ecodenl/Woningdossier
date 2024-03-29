@php
    $disabled = \App\Helpers\HoomdossierSession::isUserObserving();
@endphp
<div>
    {{-- Header row --}}
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 mb-3">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE)
                </h5>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO)
                </h5>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_LATER)
                </h5>
            </div>
        </div>
    </div>
    <div class="w-full flex flex-wrap flex-row justify-center items-center"
         @if($disabled) x-data @else x-data="draggables()" @endif>
        <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10">
            @foreach($cards as $cardCategory => $cardCollection)
                <div class="card-wrapper" x-bind="container" data-category="{{$cardCategory}}">
                    @foreach($cardCollection as $order => $card)
                        <div class="card @if($disabled) disabled pointer-events-auto @endif"
                             id="{{ $card['id'] }}" wire:key="card-{{$card['id']}}"
                             wire:loading.class="disabled" wire:target="cardMoved, cardTrashed, addHiddenCardToBoard"
                             {{-- TODO: See if undefined draggable (on tablet, caused by polyfill) can be resolved --}}
                             x-bind="draggable"
                             @if($disabled) draggable="false" @else draggable="true" @endif>
                            <div class="icon-wrapper">
                                <i class="{{ $card['icon'] ?? 'icon-tools' }}"></i>
                            </div>
                            <div class="info">
                                @if(! empty($card['route']))
                                    <a href="{{ $card['route'] }}" class="no-underline w-fit" draggable="false">
                                        <h6 class="heading-6 text-purple max-w-17/20">
                                            {{ $card['name'] }}
                                        </h6>
                                    </a>
                                @elseif(array_key_exists('index', $card))
                                    <a href="#" class="no-underline" draggable="false"
                                       x-on:click="$event.preventDefault(); window.triggerEvent(document.querySelector('#edit-{{$card['index']}}'), 'open-modal');">
                                        <h6 class="heading-6 text-purple max-w-17/20">
                                            {{ $card['name'] }}
                                        </h6>
                                    </a>
                                @else
                                    <h6 class="heading-6 max-w-17/20">
                                        {{ $card['name'] }}
                                    </h6>
                                @endif
                                <div class="flex justify-between">
                                    <p>
                                        {{-- This also triggers if both values are 0 --}}
                                        @if(empty($card['costs']['from']) && empty($card['costs']['to']))
                                            @lang('cooperation/frontend/tool.my-plan.cards.see-info')
                                        @else
                                            {{ \App\Helpers\NumberFormatter::range($card['costs']['from'], $card['costs']['to'], 0, ' - ', '€ ') }}
                                        @endif
                                    </p>
                                    <p class="font-bold">
                                        {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($card['savings'], 0, true) , '€ ') }}
                                    </p>
                                </div>
                                @if($cardCategory !== \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE)
                                    @if($card['subsidy_available'])
                                        <a href="{{ route('cooperation.frontend.tool.simple-scan.my-regulations.index', compact('cooperation', 'scan')) . "?tab=" . \App\Services\Verbeterjehuis\RegulationService::SUBSIDY }}"
                                           class="in-text w-fit" draggable="false">
                                            <div class="h-4 rounded-lg text-xs relative text-green p bg-green bg-opacity-10 flex items-center px-2">
                                                @if($card['has_user_costs'])
                                                    @lang('cooperation/frontend/tool.my-plan.cards.regulations.after-subsidy-cut')
                                                @else
                                                    @lang('cooperation/frontend/tool.my-plan.cards.regulations.subsidy-available')
                                                @endif
                                            </div>
                                        </a>
                                    @elseif($card['loan_available'])
                                        <a href="{{ route('cooperation.frontend.tool.simple-scan.my-regulations.index', compact('cooperation', 'scan')) . "?tab=" . \App\Services\Verbeterjehuis\RegulationService::LOAN }}"
                                           class="in-text w-fit" draggable="false">
                                            <div class="h-4 rounded-lg text-xs relative text-orange p bg-red bg-opacity-10 flex items-center px-2">
                                                @lang('cooperation/frontend/tool.my-plan.cards.regulations.loan-available')
                                            </div>
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <div x-data="modal()" class="absolute right-1 top-1 lg:right-3 lg:top-3"
                                 draggable="true" x-on:dragstart.prevent.stop>
                                @if(! empty($card['info']))
                                    <i class="icon-md icon-info-light clickable" x-on:click="toggle()"></i>
                                    @component('cooperation.frontend.layouts.components.modal')
                                        <div class="as-text">
                                            {!! $card['info'] !!}
                                        </div>
                                    @endcomponent
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 -mb-5">
            <div class="w-full">
                {{-- White space --}}
            </div>
            <div class="w-full flex flex-row flex-wrap justify-center items-center">
                <i class="w-20 h-20 p-4 icon-ventilation-fan mx-4 animate-spin-slow @if(empty($notifications)) hidden @endif"
                   @if(! empty($notifications)) wire:poll.1s="checkNotifications" @endif></i>
                @if(! $scan->isLiteScan() || ! \App\Helpers\Arr::isWholeArrayEmpty($hiddenCards))
                    <div x-data="modal()" class="w-20 h-20 p-4 mx-4 flex items-center justify-center">
                        <i class="icon-md icon-plus-purple clickable" x-on:click="toggle()"></i>
                        @component('cooperation.frontend.layouts.components.modal', [
                            'header' => __('cooperation/frontend/tool.my-plan.cards.add-advices.header'),
                        ])
                            <div class="w-full h-full">
                                <div class="w-full h-full space-y-2">
                                    @if(! $disabled && ! \App\Helpers\Arr::isWholeArrayEmpty($hiddenCards))
                                        <button class="btn btn-green flex w-full items-center justify-center" wire:key="trashed-button"
                                                x-on:click="window.triggerEvent(document.querySelector('#trashed'), 'open-modal'); close();">
                                            @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.trashed.button')
                                        </button>
                                    @endif
                                    @if(! $scan->isLiteScan())
                                        <button class="btn btn-green flex w-full items-center justify-center" wire:key="expert-button"
                                                x-on:click="window.triggerEvent(document.querySelector('#expert'), 'open-modal'); close();">
                                            @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.expert.button')
                                        </button>
                                    @endif
                                    @if(! $disabled && ! $scan->isLiteScan())
                                        @php $lastIndex = array_key_last($customMeasureApplicationsFormData); @endphp
                                        <button class="btn btn-green flex w-full items-center justify-center" wire:key="custom-button"
                                                x-on:click="window.triggerEvent(document.querySelector('#edit-{{$lastIndex}}'), 'open-modal'); close();">
                                            @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.add.button')
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endcomponent
                    </div>
                @endif

                <i class="w-20 h-20 p-4 mx-4 icon-trash-can-red rounded-lg transition duration-500 trash" x-bind="trash"></i>
            </div>
        </div>
    </div>

    <div>
        {{-- Modal for invisible measures --}}
        @if(! $disabled && ! \App\Helpers\Arr::isWholeArrayEmpty($hiddenCards))
            <div x-data="modal()" class="" wire:key="trashed-modal">
                @component('cooperation.frontend.layouts.components.modal', [
                    'header' => __('cooperation/frontend/tool.my-plan.cards.add-advices.options.trashed.title'),
                    'id' => 'trashed',
                ])
                    <p>
                        @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.trashed.help')
                    </p>

                    <div class="w-full h-full rounded-lg mt-4 bg-blue-100 pb-3">
                        @foreach($hiddenCards as $cardCategory => $cardCollection)
                            @if(! \App\Helpers\Arr::isWholeArrayEmpty($cardCollection))
                                <div class="card-wrapper pb-0" data-category="{{$cardCategory}}">
                                    @foreach($cardCollection as $order => $card)
                                        <div class="card clickable" id="{{ $card['id'] }}"
                                             wire:key="hidden-card-{{$card['id']}}"
                                             wire:click="addHiddenCardToBoard('{{$cardCategory}}', '{{$card['id']}}')"
                                             wire:loading.class="disabled"
                                             wire:target="cardMoved, cardTrashed, addHiddenCardToBoard">
                                            <div class="icon-wrapper">
                                                <i class="{{ $card['icon'] ?? 'icon-tools' }}"></i>
                                            </div>
                                            <div class="info">
                                                <h6 class="heading-6 max-w-17/20">
                                                    {{ $card['name'] }}
                                                </h6>
                                                <p class="-mt-1">
                                                    {{-- This also triggers if both values are 0 --}}
                                                    @if(empty($card['costs']['from']) && empty($card['costs']['to']))
                                                        @lang('cooperation/frontend/tool.my-plan.cards.see-info')
                                                    @else
                                                        {{ \App\Helpers\NumberFormatter::range($card['costs']['from'], $card['costs']['to'], 0, ' - ', '€ ') }}
                                                    @endif
                                                </p>
                                            </div>
                                            <p class="font-bold absolute right-1 bottom-1 lg:right-3 lg:bottom-3">
                                                {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($card['savings'], 0, true) , '€ ') }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endcomponent
            </div>
        @endif
        {{-- Modal for expert steps --}}
        @if(! $scan->isLiteScan())
            <div x-data="modal()" class="" wire:key="expert-modal">
                @component('cooperation.frontend.layouts.components.modal', [
                    'header' => __('cooperation/frontend/tool.my-plan.cards.add-advices.options.expert.title'),
                    'id' => 'expert',
                ])
                    <p>
                        @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.expert.help')
                    </p>

                    <ul class="mt-4 w-full text-blue-500 text-sm bg-white rounded-lg border border-blue-500 border-opacity-50 divide-y divide-blue-500 py-2 list-none pl-0">
                        @foreach(\App\Models\Step::expert()->get() as $step)
                            @if(! in_array($step->short, ['high-efficiency-boiler', 'heater', 'heat-pump']))
                                <li class="py-1 px-3">
                                    <a href="{{ route("cooperation.frontend.tool.expert-scan.index", compact('cooperation', 'step')) }}"
                                       class="in-text">
                                        <img src="{{ asset("images/icons/{$step->slug}.png") }}"
                                             alt="{{ $step->name }}" class="rounded-1/2 inline-block h-8 w-8">
                                        {{ $step->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endcomponent
            </div>
        @endif
        {{-- Modal for custom measures --}}
        @if(! $scan->isLiteScan())
            {{-- We put the modals here, else it's included in the draggable card which causes weird behaviour. --}}
            @foreach($customMeasureApplicationsFormData as $index => $data)
                <div x-data="modal">
                    @include('livewire.cooperation.frontend.layouts.parts.custom-measure-modal', [
                        'index' => $index,
                        'isNew' => $loop->last,
                        'id' => "edit-{$index}",
                        'disabled' => $disabled,
                    ])
                </div>
            @endforeach
        @endif
    </div>

    <div class="w-full grid grid-rows-2 grid-cols-3 lg:grid-rows-1 lg:grid-cols-6 grid-flow-row gap-3 mt-5 py-8 content-center border-t-2 border-b-2 border-blue-500 border-opacity-10">
{{--        <div class="w-full flex flex-wrap items-center space-x-3">--}}
{{--            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">--}}
{{--                <i class="icon-sm icon-moneybag-orange"></i>--}}
{{--            </div>--}}
{{--            <div class="flex flex-col justify-center">--}}
{{--                <span class="text-orange text-sm font-bold">--}}
{{--                    {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($expectedInvestment, 0), '€ ') }}--}}
{{--                </span>--}}
{{--                <p class="-mt-2">--}}
{{--                    @lang('cooperation/frontend/tool.my-plan.cards.investment')--}}
{{--                </p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center space-x-3">--}}
{{--            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">--}}
{{--                <i class="icon-sm icon-piggybank-green"></i>--}}
{{--            </div>--}}
{{--            <div class="flex flex-col justify-center">--}}
{{--                <span class="text-green text-sm font-bold">--}}
{{--                    {{ \App\Helpers\NumberFormatter::prefix($yearlySavings, '€ ') }}--}}
{{--                </span>--}}
{{--                <p class="-mt-2">--}}
{{--                    @lang('cooperation/frontend/tool.my-plan.cards.savings')--}}
{{--                </p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center space-x-3">--}}
{{--            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">--}}
{{--                <i class="icon-sm icon-savings"></i>--}}
{{--            </div>--}}
{{--            <div class="flex flex-col justify-center">--}}
{{--                <span class="text-blue text-sm font-bold">--}}
{{--                    {{ \App\Helpers\NumberFormatter::prefix($availableSubsidy, '€ ') }}--}}
{{--                </span>--}}
{{--                <p class="-mt-2">--}}
{{--                    Subsidie mogelijk --}}{{-- Todo: Translate using constant --}}
{{--                </p>--}}
{{--            </div>--}}
{{--        </div>--}}
        {{-- Todo: Check rating slider translations --}}
{{--        <div class="w-full flex flex-wrap items-center pr-3">--}}
{{--            @include('cooperation.frontend.layouts.parts.rating-slider', [--}}
{{--                'label' => 'Comfort', 'disabled' => true, --}}
{{--                'livewire' => true, 'inputName' => 'comfort',--}}
{{--            ])--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center">--}}
{{--            @include('cooperation.frontend.layouts.parts.rating-slider', [--}}
{{--                'label' => 'Duurzaamheid', 'disabled' => true, --}}
{{--                'livewire' => true, 'inputName' => 'renewable',--}}
{{--            ])--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center">--}}
{{--            @include('cooperation.frontend.layouts.parts.rating-slider', [--}}
{{--                'label' => 'Goede investering', 'disabled' => true, --}}
{{--                'livewire' => true, 'inputName' => 'investment',--}}
{{--            ])--}}
{{--        </div>--}}
    </div>
</div>