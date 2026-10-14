{{-- "Gekozen pakket" under the columns of a SmartTwin woonplan; see App\Services\SmartTwin\PackageResults. --}}
@php
    /** @var \App\Services\SmartTwin\PackageResults $results */
    // Not rounded to the woonplan's steps of five: these are SmartTwin's figures, shown as given.
    $euro = fn ($amount) => \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format(round((float) $amount), 0), '€ ');
    $number = fn ($amount) => \App\Helpers\NumberFormatter::format(round((float) $amount), 0);
    $from = fn ($now, $after, $unit) => trim((is_null($now) ? '' : $number($now) . ' → ') . (is_null($after) ? '–' : $number($after)) . ' ' . $unit);
@endphp
<div class="w-full mt-5 p-5 rounded-lg border-2 border-blue-500 border-opacity-10" id="package-results">
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h5 class="heading-5 mb-3">@lang('cooperation/frontend/tool.my-plan.package-results.title')</h5>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-right font-bold">@lang('cooperation/frontend/tool.my-plan.package-results.costs')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results->lines as $line)
                        <tr @class(['font-bold' => $line['package'], 'italic' => ! $line['package']])>
                            <td class="pr-3">{{ $line['name'] }}</td>
                            <td class="text-right whitespace-nowrap">
                                @if(empty($line['from']) && empty($line['to']))
                                    @lang('cooperation/frontend/tool.my-plan.package-results.no-costs')
                                @elseif(! empty($line['from']) && ! empty($line['to']) && $line['from'] != $line['to'])
                                    {{ \App\Helpers\NumberFormatter::range($line['from'], $line['to'], 0, ' - ', '€ ') }}
                                @else
                                    {{ $euro($line['to'] ?: $line['from']) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="font-bold border-t border-blue-500 border-opacity-50">
                        <td class="pr-3 pt-1">@lang('cooperation/frontend/tool.my-plan.package-results.total')</td>
                        <td class="text-right pt-1 whitespace-nowrap">{{ $euro($results->total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-sm space-y-1">
            @if($results->intact)
                @unless(is_null($results->savingsPerYear))
                    <p class="font-bold">
                        @lang('cooperation/frontend/tool.my-plan.package-results.savings'):
                        {{ $euro($results->savingsPerYear) }} @lang('cooperation/frontend/tool.my-plan.package-results.per-year')
                    </p>
                @endunless
                @unless(is_null($results->subsidy))
                    <p class="font-bold">
                        @lang('cooperation/frontend/tool.my-plan.package-results.subsidy'): {{ $euro($results->subsidy) }}
                        @if(! empty($results->subsidies))
                            <span class="font-normal">({{ collect($results->subsidies)->pluck('name')->filter()->implode(', ') }})</span>
                        @endif
                    </p>
                @endunless
                @unless(is_null($results->labelAfter))
                    <p>
                        @lang('cooperation/frontend/tool.my-plan.package-results.energy-label'):
                        {{ trim(($results->labelNow ? $results->labelNow . ' → ' : '') . $results->labelAfter) }}
                    </p>
                @endunless
                <p>@lang('cooperation/frontend/tool.my-plan.package-results.gas'): {{ $from($results->gasNow, $results->gasAfter, 'm³') }} @lang('cooperation/frontend/tool.my-plan.package-results.per-year')</p>
                <p>@lang('cooperation/frontend/tool.my-plan.package-results.electricity'): {{ $from($results->electricityNow, $results->electricityAfter, 'kWh') }} @lang('cooperation/frontend/tool.my-plan.package-results.per-year')</p>
                @unless(is_null($results->co2Savings))
                    <p>@lang('cooperation/frontend/tool.my-plan.package-results.co2-savings'): {{ $number($results->co2Savings) }} kg @lang('cooperation/frontend/tool.my-plan.package-results.per-year')</p>
                @endunless
            @else
                {{-- What SmartTwin calculated only holds for the package as a whole. --}}
                <p class="font-bold">
                    @lang('cooperation/frontend/tool.my-plan.package-results.broken')
                    @if($this->canHandOff)
                        <a href="#" class="in-text"
                           x-on:click.prevent="document.querySelector('#smarttwin-recalculate').triggerEvent('open-modal');">
                            @lang('cooperation/frontend/tool.my-plan.package-results.broken-link')
                        </a>
                    @endif
                </p>
            @endif
        </div>
    </div>

    @if($this->canHandOff)
        <div x-data="modal()" class="w-full mt-5">
            <button type="button" class="btn btn-green w-full" x-on:click="toggle()">
                @lang('cooperation/frontend/tool.my-plan.package-results.adjust')
            </button>
            @component('cooperation.frontend.layouts.components.modal', [
                'header' => __('cooperation/frontend/tool.my-plan.package-results.adjust'),
                'id' => 'smarttwin-recalculate',
            ])
                <p class="mb-4">@lang('cooperation/frontend/tool.my-plan.package-results.adjust-warning')</p>
                {{-- New tab, as the hand-off on the woonplan page does: the woonplan stays open behind SmartTwin. --}}
                <form method="POST" target="_blank" rel="noopener"
                      action="{{ route('cooperation.frontend.tool.simple-scan.my-plan.smarttwin', ['cooperation' => $cooperation, 'scan' => $scan]) }}"
                      class="flex justify-end space-x-3">
                    @csrf
                    <button type="button" class="btn btn-outline-green" x-on:click="close()">
                        @lang('cooperation/frontend/tool.my-plan.package-results.adjust-cancel')
                    </button>
                    <button type="submit" class="btn btn-green" x-on:click="close()">
                        @lang('cooperation/frontend/tool.my-plan.package-results.adjust-confirm')
                    </button>
                </form>
            @endcomponent
        </div>
    @endif
</div>
