<div>
    {{-- Header row --}}
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 mb-3" x-data="modal()">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-blue-800 rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-blue-800">
                    In orde
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer" x-on:click="toggle()"
               wire:click="setCategory('{{$CATEGORY_COMPLETE}}')"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-green rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-green">
                    Nu aanpakken
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer" x-on:click="toggle()"
               wire:click="setCategory('{{$CATEGORY_TO_DO}}')"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-yellow rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-yellow">
                    Later uitvoeren
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer" x-on:click="toggle()"
               wire:click="setCategory('{{$CATEGORY_LATER}}')"></i>
        </div>
        @component('cooperation.frontend.layouts.components.modal', ['header' => __('cooperation/frontend/tool.form.subject')])
            <form wire:submit.prevent="submit()">
                <div class="flex flex-wrap mb-5">
                    @component('cooperation.frontend.layouts.components.form-group', [
                       'inputName' => 'new_measure.subject',
                       'class' => 'w-full -mt-4 mb-4',
                       'id' => 'new-measure-subject',
                       'withInputSource' => false,
                   ])
                        <input class="form-input" wire:model="new_measure.subject" id="new-measure-subject"
                               placeholder="Placeholder">
                    @endcomponent
                    <div class="w-full flex items-center">
                        <i class="icon-sm icon-info mr-3"></i>
                        <h6 class="heading-6">
                            Prijsindicatie in €
                        </h6>
                    </div>
                    @component('cooperation.frontend.layouts.components.form-group', [
                                    'inputName' => 'new_measure.price.from',
                                    'class' => 'w-1/2 pr-1',
                                    'id' => 'new-measure-price-from',
                                    'withInputSource' => false,
                                ])
                        <input class="form-input" wire:model="new_measure.price.from" id="new-measure-price-from"
                               placeholder="van">
                    @endcomponent
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'inputName' => 'new_measure.price.to',
                        'class' => 'w-1/2 pl-1',
                        'id' => 'new-measure-price-to',
                        'withInputSource' => false,
                    ])
                        <input class="form-input" wire:model="new_measure.price.to" id="new-measure-price-to"
                               placeholder="tot">
                    @endcomponent
                </div>
                <div class="w-full border border-gray fixed left-0"></div>
                <div class="flex flex-wrap justify-center mt-14">
                    <button class="btn btn-purple w-full" type="submit">
                        <i class="icon-xs icon-plus-purple mr-3"></i>
                        Voeg maatregel toe
                    </button>
                </div>
            </form>
        @endcomponent
    </div>
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10"
         x-data="draggables()"
         x-on:item-dragged.window="livewire.emit('cardMoved', $event.detail.from.getAttribute('data-category'), $event.detail.to.getAttribute('data-category'), $event.detail.id)">
        @foreach($cards as $cardCategory => $cardCollection)
            <div class="card-wrapper" x-bind="container" data-category="{{$cardCategory}}"
                 x-on:drop.prevent="let placeholder = $el.querySelector('.card-placeholder');
                 $el.removeChild(placeholder); $el.appendChild(placeholder)">
                @foreach($cardCollection as $id => $card)
                    <div class="card" id="{{ $id }}"
                         x-bind="draggable" draggable="true"
                         x-on:drag="$el.classList.remove('card'); $el.classList.add('card-placeholder');"
                         x-on:dragend="$el.classList.remove('card-placeholder'); $el.classList.add('card');
                         ">
                        <div class="icon-wrapper">
                            <i class="{{ $card['icon'] ?? 'icon-tools' }}"></i>
                        </div>
                        <div class="center-info">
                            <h6 class="heading-6">{{ $card['name'] }}</h6>
                            <p class="-mt-1">
                                @if(empty($card['price']['from']) && empty($card['price']['to']))
                                    Zie info
                                @else
                                    {{ \App\Helpers\NumberFormatter::range($card['price']['from'] ?? '', $card['price']['to'] ?? '', 0, ' - ', '€ ') }}
                                @endif
                            </p>
                            <?php $subsidy = $card['subsidy'] ?? ''; ?>
                            @if($subsidy == $SUBSIDY_AVAILABLE)
                                <div class="h-4 rounded-lg text-xs relative text-green p bg-green bg-opacity-10 flex items-center px-2"
                                     style="width: fit-content; width: -moz-fit-content;">
                                    Subsidie mogelijk
                                </div>
                            @elseif($subsidy == $SUBSIDY_UNAVAILABLE)
                                <div class="h-4 rounded-lg text-xs relative text-red p bg-red bg-opacity-10 flex items-center px-2"
                                     style="width: fit-content; width: -moz-fit-content;">
                                    Geen subsidie
                                </div>
                            @endif
                        </div>
                        <div class="end-info">
                            <div x-data="modal()">
                                @if(! empty($card['info']))
                                    <i class="icon-md icon-info-light" x-on:click="toggle()"></i>
                                    @component('cooperation.frontend.layouts.components.modal')
                                        {!! $card['info'] !!}
                                    @endcomponent
                                @endif
                            </div>
                            <p class="font-bold">{{ \App\Helpers\NumberFormatter::prefix($card['savings'] ?? 0, '€ ') }}</p>
                        </div>
                    </div>
                @endforeach
                <div class="card-placeholder">

                </div>
            </div>
        @endforeach
    </div>
</div>