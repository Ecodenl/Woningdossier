<div>
    {{-- Header row --}}
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 mb-3">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-blue-800 rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-blue-800">
                    In orde
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-green rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-green">
                    Nu aanpakken
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-yellow rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-yellow">
                    Later uitvoeren
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
    </div>
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10"
         x-data="draggables()">
        @foreach($cards as $cardCategory => $cardCollection)
            <div class="card-wrapper" x-bind="container"
                 x-on:drop.prevent="let placeholder = $el.querySelector('.card-placeholder');
                 $el.removeChild(placeholder); $el.appendChild(placeholder); ">
                @foreach($cardCollection as $card)
                    <div class="card" id="{{ \Illuminate\Support\Str::random() }}"
                         x-bind="draggable"
                         x-on:drag="$el.classList.remove('card'); $el.classList.add('card-placeholder');"
                         x-on:dragend="$el.classList.remove('card-placeholder'); $el.classList.add('card');">
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
                            <?php
                                $subsidy = $card['subsidy'] ?? '';
                            ?>
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
