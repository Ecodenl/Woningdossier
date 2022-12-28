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
         @if(! \App\Helpers\HoomdossierSession::isUserObserving()) x-data="draggables()" @endif
         x-on:draggable-dragged.window="livewire.emitTo('cooperation.frontend.tool.quick-scan.my-plan.form', 'cardMoved', $event.detail.from.getAttribute('data-category'), $event.detail.to.getAttribute('data-category'), $event.detail.id, $event.detail.order)"
         x-on:draggable-trashed.window="livewire.emitTo('cooperation.frontend.tool.quick-scan.my-plan.form', 'cardTrashed', $event.detail.from.getAttribute('data-category'), $event.detail.id)">
        <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10">
            @foreach($cards as $cardCategory => $cardCollection)
                <div class="card-wrapper" x-bind="container" data-category="{{$cardCategory}}">
                    @foreach($cardCollection as $order => $card)
                        <div class="card @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled @endif"
                             id="{{ $card['id'] }}" wire:key="card-{{$card['id']}}"
                             x-on:draggable-dragged.window="$el.classList.add('disabled');"
                             x-on:draggable-trashed.window="$el.classList.add('disabled');"
                             x-on:draggable-readded.window="$el.classList.add('disabled');"
                             x-on:moved-card="$el.classList.remove('disabled');"
                             x-on:trashed-card="$el.classList.remove('disabled');"
                             x-on:readded-card="$el.classList.remove('disabled');"
                             {{-- TODO: See if undefined draggable (on tablet, caused by polyfill) can be resolved --}}
                             x-bind="draggable"
                             @if(\App\Helpers\HoomdossierSession::isUserObserving()) draggable="false" @else draggable="true" @endif>
                            <div class="icon-wrapper">
                                <i class="{{ $card['icon'] ?? 'icon-tools' }}"></i>
                            </div>
                            <div class="info">
                                @if(! empty($card['route']))
                                    <a href="{{ $card['route'] }}" class="no-underline" draggable="false">
                                        <h6 class="heading-6 text-purple max-w-17/20">
                                            {{ $card['name'] }}
                                        </h6>
                                    </a>
                                @else
                                    <h6 class="heading-6 max-w-17/20">
                                        {{ $card['name'] }}
                                    </h6>
                                @endif
                                <p class="-mt-1">
                                    {{-- This also triggers if both values are 0 --}}
                                    @if(empty($card['costs']['from']) && empty($card['costs']['to']))
                                        @lang('cooperation/frontend/tool.my-plan.cards.see-info')
                                    @else
                                        {{ \App\Helpers\NumberFormatter::range($card['costs']['from'], $card['costs']['to'], 0, ' - ', '€ ') }}
                                    @endif
                                </p>
    <!--
                                <?php $subsidy = $card['subsidy'] ?? ''; ?>
                                @if($subsidy == $SUBSIDY_AVAILABLE)
                                    <div class="h-4 rounded-lg text-xs relative text-green p bg-green bg-opacity-10 flex items-center px-2"
                                         style="width: fit-content; width: -moz-fit-content;">
                                        Subsidie mogelijk {{-- Todo: Translate using constant --}}
                                    </div>
                                @elseif($subsidy == $SUBSIDY_UNAVAILABLE)
                                    <div class="h-4 rounded-lg text-xs relative text-red p bg-red bg-opacity-10 flex items-center px-2"
                                         style="width: fit-content; width: -moz-fit-content;">
                                        Geen subsidie {{-- Todo: Translate using constant --}}
                                    </div>
                                @endif
    -->
                            </div>
                            <div x-data="modal()" class="absolute right-1 top-1 lg:right-3 lg:top-3"
                                 draggable="true" x-on:dragstart.prevent.stop>
                                @if(! empty($card['info']))
                                    <i class="icon-md icon-info-light clickable" x-on:click="toggle()"></i>
                                    @component('cooperation.frontend.layouts.components.modal')
                                        {!! $card['info'] !!}
                                    @endcomponent
                                @endif
                            </div>
                            <p class="font-bold absolute right-1 bottom-1 lg:right-3 lg:bottom-3">
                                {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($card['savings'], 0, true) , '€ ') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 -mb-5">
            <div class="w-full">
                {{-- White space --}}
            </div>
            <div class="w-full flex flex-row flex-wrap justify-center items-center space-x-5">
                <div x-data="modal()" class="">
                    <i class="icon-md icon-plus-purple clickable mt-3" x-on:click="toggle()"></i>
                    @component('cooperation.frontend.layouts.components.modal', [
                        'header' => __('cooperation/frontend/tool.my-plan.cards.add-advices.header'),
                    ])
                        <div class="w-full h-full">
                            <div class="w-full h-full space-y-2">
                                @if(! \App\Helpers\HoomdossierSession::isUserObserving() && ! \App\Helpers\Arr::isWholeArrayEmpty($hiddenCards))
                                    <button class="btn btn-green flex w-full items-center justify-center" wire:key="trashed-button"
                                            x-on:click="window.triggerEvent(document.querySelector('#trashed'), 'open-modal'); close();">
                                        @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.trashed.button')
                                    </button>
                                @endif
                                <button class="btn btn-green flex w-full items-center justify-center" wire:key="expert-button"
                                        x-on:click="window.triggerEvent(document.querySelector('#expert'), 'open-modal'); close();">
                                    @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.expert.button')
                                </button>
                                @if(! \App\Helpers\HoomdossierSession::isUserObserving())
                                    <button class="btn btn-green flex w-full items-center justify-center" wire:key="custom-button"
                                            x-on:click="window.triggerEvent(document.querySelector('#add'), 'open-modal'); close();">
                                        @lang('cooperation/frontend/tool.my-plan.cards.add-advices.options.add.button')
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endcomponent
                </div>

                {{-- Modal for invisible measures --}}
                @if(! \App\Helpers\HoomdossierSession::isUserObserving() && ! \App\Helpers\Arr::isWholeArrayEmpty($hiddenCards))
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
                                                     x-on:click="window.triggerCustomEvent($el, 'draggable-readded');"
                                                     wire:click="$emitTo('cooperation.frontend.tool.quick-scan.my-plan.form', 'addHiddenCardToBoard', '{{$cardCategory}}', '{{$card['id']}}')"
                                                     x-on:draggable-dragged.window="$el.classList.add('disabled');"
                                                     x-on:draggable-trashed.window="$el.classList.add('disabled');"
                                                     x-on:draggable-readded.window="$el.classList.add('disabled');"
                                                     x-on:moved-card="$el.classList.remove('disabled');"
                                                     x-on:trashed-card="$el.classList.remove('disabled');"
                                                     x-on:readded-card="$el.classList.remove('disabled');">
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
                {{-- Modal for custom measures --}}
                @if(! \App\Helpers\HoomdossierSession::isUserObserving())
                    <div x-data="modal()" class="" wire:key="custom-modal">
                        @component('cooperation.frontend.layouts.components.modal', [
                            'header' => __('cooperation/frontend/tool.form.subject'),
                            'id' => 'add',
                        ])
                            <form wire:submit.prevent="submit()">
                                <div class="flex flex-wrap mb-5">
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                       'inputName' => 'custom_measure_application.name',
                                       'class' => 'w-full -mt-4 mb-4',
                                       'id' => 'custom-measure-application-name',
                                       'withInputSource' => false,
                                   ])
                                        <input class="form-input" wire:model="custom_measure_application.name" id="custom-measure-application-name"
                                               placeholder="@lang('cooperation/frontend/shared.modals.add-measure.subject-placeholder')">
                                    @endcomponent
                                    <div class="w-full flex items-center">
                                        <i class="icon-sm icon-info mr-3"></i>
                                        <h6 class="heading-6">
                                            @lang('cooperation/frontend/shared.modals.add-measure.info')
                                        </h6>
                                    </div>
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                       'inputName' => "custom_measure_application.info",
                                       'class' => 'w-full mb-4',
                                       'id' => 'custom-measure-application-info',
                                       'withInputSource' => false,
                                   ])
                                        <textarea class="form-input" wire:model="custom_measure_application.info"
                                                  id="custom-measure-application-info"
                                                  placeholder="@lang('cooperation/frontend/shared.modals.add-measure.info-placeholder')"
                                        ></textarea>
                                    @endcomponent
                                    <div class="w-full flex items-center">
                                        <i class="icon-sm icon-info mr-3"></i>
                                        <h6 class="heading-6">
                                            @lang('cooperation/frontend/shared.modals.add-measure.costs')
                                        </h6>
                                    </div>
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                        'inputName' => 'custom_measure_application.costs.from',
                                        'class' => 'w-1/2 pr-1 mb-4',
                                        'id' => 'custom-measure-application-costs-from',
                                        'withInputSource' => false,
                                    ])
                                        <input class="form-input" wire:model="custom_measure_application.costs.from" id="custom-measure-application-costs-from"
                                               placeholder="@lang('default.from')">
                                    @endcomponent
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                        'inputName' => 'custom_measure_application.costs.to',
                                        'class' => 'w-1/2 pl-1 mb-4',
                                        'id' => 'custom-measure-application-costs-to',
                                        'withInputSource' => false,
                                    ])
                                        <input class="form-input" wire:model="custom_measure_application.costs.to" id="custom-measure-application-costs-to"
                                               placeholder="@lang('default.to')">
                                    @endcomponent
                                    <div class="w-full flex items-center">
                                        <i class="icon-sm icon-info mr-3"></i>
                                        <h6 class="heading-6">
                                            @lang('cooperation/frontend/shared.modals.add-measure.savings-money')
                                        </h6>
                                    </div>
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                        'inputName' => 'custom_measure_application.savings_money',
                                        'class' => 'w-full mb-4',
                                        'id' => 'custom-measure-application-savings-money',
                                        'withInputSource' => false,
                                    ])
                                        <input class="form-input" wire:model="custom_measure_application.savings_money"
                                               id="custom-measure-application-savings-money"
                                               placeholder="@lang('cooperation/frontend/shared.modals.add-measure.savings-money')">
                                    @endcomponent
                                </div>
                                <div class="w-full border border-gray fixed left-0"></div>
                                <div class="flex flex-wrap justify-center mt-14">
                                    <button class="btn btn-purple w-full" type="submit">
                                        <i class="icon-xs icon-plus-purple mr-3"></i>
                                        @lang('cooperation/frontend/shared.modals.add-measure.save')
                                    </button>
                                </div>
                            </form>
                        @endcomponent
                    </div>
                @endif

                <i class="w-20 h-20 icon-trash-can-red p-4 rounded-lg transition duration-500 trash" x-bind="trash"></i>
            </div>
        </div>
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
{{--                'label' => 'Comfort', 'disabled' => true, 'default' => $comfort,--}}
{{--                'livewire' => true, 'inputName' => 'comfort',--}}
{{--            ])--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center">--}}
{{--            @include('cooperation.frontend.layouts.parts.rating-slider', [--}}
{{--                'label' => 'Duurzaamheid', 'disabled' => true, 'default' => $renewable,--}}
{{--                'livewire' => true, 'inputName' => 'renewable',--}}
{{--            ])--}}
{{--        </div>--}}
{{--        <div class="w-full flex flex-wrap items-center">--}}
{{--            @include('cooperation.frontend.layouts.parts.rating-slider', [--}}
{{--                'label' => 'Goede investering', 'disabled' => true, 'default' => $investment,--}}
{{--                'livewire' => true, 'inputName' => 'investment',--}}
{{--            ])--}}
{{--        </div>--}}
    </div>
</div>