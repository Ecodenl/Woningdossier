@php
    // 'date' or 'datetime'.
    $mode ??= 'date';
    $format = $mode === 'datetime' ? 'Y-m-d H:i' : 'Y-m-d';

     /** @var bool $livewire */
    $livewire = $livewire ?? false;
    /** @var string $name */
    $name = $name ?? 'date';
    /** @var \Carbon\Carbon|null $date */
    $date = (($date ?? null) instanceof \Carbon\Carbon) ? $date->format($format) : '';

    $htmlName = Str::convertDotToHtml($name);
@endphp

<div class="antialiased sans-serif w-full">
    <div x-data="datepicker('{{$name}}', @js($mode === 'datetime'))" x-cloak class="datepicker">
        <div class="relative">
            {{-- Input that holds the value --}}
            <input type="text" x-ref="{{ $name }}" class="datepicker-value hidden"
                   @if($livewire) wire:model.lazy="{{$name}}" @else value="{{ $date }}" name="{{ $htmlName }}" @endif>

            {{-- Visual input --}}
            <input type="text"
                   {{--TODO: When adding this, and clicking the label if the picker is open, the picker won't close unless you explicitly click the input... --}}
{{--                   @if(! empty($id)) id="{{ $id }}" @endif--}}
                   readonly
                   x-model="datepickerValue"
                   x-on:click="showDatepicker = !showDatepicker; if (showDatepicker) { mode = 'day'; }"
                   x-on:keydown.escape="showDatepicker = false"
                   class="form-input cursor-pointer w-full font-medium"
                   placeholder="{{ $placeholder ?? 'Select date' }}" wire:ignore>

            {{-- Calendar icon in input --}}
            <div class="absolute top-1 right-0 px-3 py-1 cursor-pointer" x-on:click="showDatepicker = !showDatepicker">
                <i class="icon-md icon-calendar"></i>
            </div>

            {{-- Datepicker --}}
            <div class="bg-white mt-12 rounded-lg shadow p-4 absolute top-0 left-0 z-10"
                 style="width: 17rem"
                 x-show.transition="showDatepicker"
                 x-on:click.outside="showDatepicker = false">

                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span x-text="window.months[month]" x-on:click="mode = mode === 'day' ? 'month' : 'day'"
                              class="block text-md font-bold text-gray-800 cursor-pointer"></span>
                        <span x-text="year" class="text-md text-gray-600 font-normal"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button"
                                class="clickable inline-flex cursor-pointer hover:bg-blue-200/25 p-1 rounded-full mr-2"
                                x-on:click="leftClick()">
                            <i class="icon-sm icon-arrow-down transform rotate-90"></i>
                        </button>
                        <button type="button"
                                class="clickable inline-flex cursor-pointer hover:bg-blue-200/25 p-1 rounded-full ml-2"
                                x-on:click="rightClick()">
                            <i class="icon-sm icon-arrow-down transform -rotate-90"></i>
                        </button>
                    </div>
                </div>

                <div x-show="mode === 'month'">
                    <div class="flex flex-wrap mb-3 -mx-1">
                        {{-- The selectable months --}}
                        <template x-for="(monthName, index) in window.months" :key="index">
                            <div class="p-1 mb-1 mx-1 rounded border border-blue/50 cursor-pointer hover:border-blue/25"
                                 style="width: 30%" x-on:click="setMonth(index)">
                                <div x-text="monthName"
                                     class="text-blue-500 font-medium text-center text-xs"></div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end w-full mt-2">
                        <i class="icon-sm icon-trash-can-red clickable" x-on:click="setDate(''); showDatepicker = false;"></i>
                    </div>
                </div>

                <div x-show="mode === 'day'">
                    <div class="flex flex-wrap mb-3 -mx-1">
                        {{-- The day short headers (ma / di / etc.) --}}
                        <template x-for="(dayShort, index) in window.days" :key="index">
                            <div style="width: 14.26%" class="px-1">
                                <div x-text="dayShort"
                                     class="text-blue-500 font-medium text-center text-sm"></div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-wrap -mx-1">
                        {{-- The days of the prior month (no content) --}}
                        <template x-for="blankDay in blankDays">
                            <div
                                style="width: 14.28%"
                                class="text-center border p-1 border-transparent text-sm"
                            ></div>
                        </template>
                        {{-- The selectable days of the current month --}}
                        <template x-for="(day, dateIndex) in no_of_days" :key="dateIndex">
                            <div style="width: 14.28%" class="px-1 mb-1">
                                <div
                                    x-on:click="setDateValue(day)"
                                    x-text="day"
                                    class="day-selector cursor-pointer text-center text-sm rounded-full leading-loose transition ease-in-out duration-100"
                                    x-bind:class="{
                                        'bg-green-200 text-white': isSelected(day),
                                        'bg-blue-800 text-white': isToday(day) && ! isSelected(day),
                                        'text-blue hover:bg-blue-500/25': ! isToday(day) && ! isSelected(day),
                                    }"
                                ></div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end w-full mt-2">
                        <i class="icon-sm icon-trash-can-red clickable" x-on:click="setDate(''); showDatepicker = false;"></i>
                    </div>

                    {{-- Hours and minutes --}}
                    @if($mode === 'datetime')
                        <hr>

                        <div class="flex flex-wrap justify-between items-center w-full space-x-2">
                            <input class="form-input hours" x-model.number="hours" type="number" min="0" max="23">
                            <input class="form-input minutes" x-model.number="minutes" type="number" min="0" max="59">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@pushonce('css')
    <script type="module" nonce="{{ $cspNonce }}">
        window.days = @json(array_values(__('default.day-shorts')));
        window.months = @json(array_values(__('default.months')));
    </script>
@endpushonce
