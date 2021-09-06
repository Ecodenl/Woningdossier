<div>
    {{-- Header row --}}
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 mb-3 px-3 lg:px-8" x-data="modal()">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE)
                </h5>
            </div>
            <i class="icon-md icon-plus-circle clickable" x-on:click="toggle()"
               wire:click="setCategory('{{\App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE}}')"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO)
                </h5>
            </div>
            <i class="icon-md icon-plus-circle clickable" x-on:click="toggle()"
               wire:click="setCategory('{{\App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO}}')"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <h5 class="heading-5">
                    @lang("cooperation/frontend/tool.my-plan.categories." . \App\Services\UserActionPlanAdviceService::CATEGORY_LATER)
                </h5>
            </div>
            <i class="icon-md icon-plus-circle clickable" x-on:click="toggle()"
               wire:click="setCategory('{{\App\Services\UserActionPlanAdviceService::CATEGORY_LATER}}')"></i>
        </div>
        @component('cooperation.frontend.layouts.components.modal', ['header' => __('cooperation/frontend/tool.form.subject')])
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
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 px-3 lg:px-8"
         x-data="draggables()"
         x-on:item-dragged.window="livewire.emit('cardMoved', $event.detail.from.getAttribute('data-category'), $event.detail.to.getAttribute('data-category'), $event.detail.id, $event.detail.order)">
        @foreach($cards as $cardCategory => $cardCollection)
            <div class="card-wrapper" x-bind="container" data-category="{{$cardCategory}}">
                @foreach($cardCollection as $order => $card)
                    <div class="card" id="{{ $card['id'] }}"
                         x-bind="draggable" draggable="true">
                        <div class="icon-wrapper">
                            <i class="{{ $card['icon'] ?? 'icon-tools' }}"></i>
                        </div>
                        <div class="info">
                            @if(! empty($card['route']))
                                <a href="{{ $card['route'] }}" class="no-underline" draggable="false">
                                    <h6 class="heading-6 text-purple">
                                        {{ $card['name'] }}
                                    </h6>
                                </a>
                            @else
                                <h6 class="heading-6">
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
                        <div x-data="modal()" class="absolute right-1 top-1 lg:right-3 lg:top-3">
                            @if(! empty($card['info']))
                                <i class="icon-md icon-info-light clickable" x-on:click="toggle()"></i>
                                @component('cooperation.frontend.layouts.components.modal')
                                    {!! $card['info'] !!}
                                @endcomponent
                            @endif
                        </div>
                        <p class="font-bold absolute right-1 bottom-1 lg:right-3 lg:bottom-3">
                            {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($card['savings'], 1) , '€ ') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="w-full grid grid-rows-2 grid-cols-3 lg:grid-rows-1 lg:grid-cols-6 grid-flow-row gap-3 mt-5 px-3 py-8 lg:px-8 content-center border-t-2 border-b-2 border-blue-500 border-opacity-10">
        <div class="w-full flex flex-wrap items-center space-x-3">
            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">
                <i class="icon-sm icon-moneybag-orange"></i>
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-orange text-sm font-bold">
                    {{ \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($investment, 0), '€ ') }}
                </span>
                <p class="-mt-2">
                    @lang('cooperation/frontend/tool.my-plan.cards.investment')
                </p>
            </div>
        </div>
        <div class="w-full flex flex-wrap items-center space-x-3">
            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">
                <i class="icon-sm icon-piggybank-green"></i>
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-green text-sm font-bold">
                    {{ \App\Helpers\NumberFormatter::prefix($yearlySavings, '€ ') }}
                </span>
                <p class="-mt-2">
                    @lang('cooperation/frontend/tool.my-plan.cards.savings')
                </p>
            </div>
        </div>
        <div class="w-full flex flex-wrap items-center space-x-3">
<!--
            <div class="rounded-full bg-blue bg-opacity-10 w-8 h-8 flex justify-center items-center">
                <i class="icon-sm icon-savings"></i>
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-blue text-sm font-bold">
                    {{ \App\Helpers\NumberFormatter::prefix($availableSubsidy, '€ ') }}
                </span>
                <p class="-mt-2">
                    Subsidie mogelijk {{-- Todo: Translate using constant --}}
                </p>
            </div>
-->
        </div>
        {{-- Todo: Check rating slider translations --}}
        <div class="w-full flex flex-wrap items-center pr-3">
            @include('cooperation.frontend.layouts.parts.rating-slider', [
                'label' => 'Comfort', 'default' => 5, 'disabled' => true
            ])
        </div>
        <div class="w-full flex flex-wrap items-center">
            @include('cooperation.frontend.layouts.parts.rating-slider', [
                'label' => 'Duurzaamheid', 'default' => 4, 'disabled' => true
            ])
        </div>
        <div class="w-full flex flex-wrap items-center">
            @include('cooperation.frontend.layouts.parts.rating-slider', [
                'label' => 'Goede investering', 'default' => 3, 'disabled' => true
            ])
        </div>
    </div>
    <div class="w-full flex flex-wrap bg-blue-100 pb-8 px-3 lg:px-8 items-center">
        @php
            $disableResident = $currentInputSource->short !== $residentInputSource->short;
            $disableCoach = $currentInputSource->short !== $coachInputSource->short;
        @endphp
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __('cooperation/frontend/tool.my-plan.comments.resident'),
            'class' => 'w-full md:w-1/2 md:pr-3',
            'withInputSource' => false,
            'id' => 'comments-resident',
            'inputName' => 'comments.resident'
        ])
            <textarea id="comments-resident" class="form-input" wire:model="residentCommentText"
                      @if($disableResident) disabled @endif
                      placeholder="@lang('default.form.input.comment-placeholder')"></textarea>
            <button class="btn btn-purple absolute right-3 bottom-7" @if($disableResident) disabled @endif
                    wire:click="saveComment('{{\App\Models\InputSource::RESIDENT_SHORT}}')"
                    wire:loading.attr="disabled" wire:target="saveComment">
                @lang('default.buttons.save')
            </button>
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __('cooperation/frontend/tool.my-plan.comments.coach'),
            'class' => 'w-full md:w-1/2 md:pl-3',
            'withInputSource' => false,
            'id' => 'comments-coach',
            'inputName' => 'comments.coach'
        ])
            <textarea id="comments-coach" class="form-input" wire:model="coachCommentText"
                      @if($disableCoach) disabled @endif
                      placeholder="@lang('default.form.input.comment-placeholder')"></textarea>
            <button class="btn btn-purple absolute right-3 bottom-7" @if($disableCoach) disabled @endif
                    wire:click="saveComment('{{\App\Models\InputSource::COACH_SHORT}}')"
                    wire:loading.attr="disabled" wire:target="saveComment">
                @lang('default.buttons.save')
            </button>
        @endcomponent
    </div>
</div>