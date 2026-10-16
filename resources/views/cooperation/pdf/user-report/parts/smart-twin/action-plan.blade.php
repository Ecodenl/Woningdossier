{{--
    The woonplan page of the report once the woonplan is SmartTwin's: what the woonplan shows (decision
    2026-10-15-smarttwin-rapport-na-omslagpunt, P1, P2). See App\Services\SmartTwin\WoonplanReport.
--}}
@php
    /** @var \App\Services\SmartTwin\WoonplanReport $woonplanReport */
    $results = $woonplanReport->results;
    // Whole euros, a line exactly as its card; see App\Services\SmartTwin\CardCosts.
    $euro = fn ($amount) => \App\Helpers\NumberFormatter::prefix(\App\Helpers\NumberFormatter::format($amount, 0), '€ ');
    $costs = fn ($from, $to) => (empty($from) && empty($to))
        ? __('cooperation/frontend/tool.my-plan.package-results.no-costs')
        : \App\Helpers\NumberFormatter::range($from, $to, 0, ' - ', '€ ');
    $number = fn ($amount) => is_null($amount) ? '–' : \App\Helpers\NumberFormatter::format(round((float) $amount), 0);
    $nowAfter = fn ($now, $after, $unit) => trim((is_null($now) ? '' : $number($now) . ' → ') . $number($after) . ' ' . $unit);
    $package = 'cooperation/frontend/tool.my-plan.package-results';
@endphp

{{-- SmartTwin's calculation of the dwelling as it is, the one its package is compared with (P2). --}}
<div class="group">
    <h3>
        {{ strip_tags(__('pdf/user-report.pages.action-plan.usage.current')) }}
    </h3>
    <div class="row">
        <div class="col-2">
            <p>{{ Str::ucfirst(strip_tags(__('general.unit.gas.title'))) }}</p>
        </div>
        <div class="col-10">
            <p>{{ $number($results->gasNow) }} m<sup>3</sup> @lang("{$package}.per-year")</p>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <p>{{ Str::ucfirst(strip_tags(__('general.unit.electric.title'))) }}</p>
        </div>
        <div class="col-10">
            <p>{{ $number($results->electricityNow) }} kWh @lang("{$package}.per-year")</p>
        </div>
    </div>
    <p>@lang('pdf/user-report.pages.action-plan.smart-twin.usage-source')</p>
</div>

<div class="group">
    <h3>
        {{ strip_tags(__('pdf/user-report.pages.action-plan.categories.' . \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO)) }}
    </h3>
    <h4>@lang("{$package}.title")</h4>

    <div class="row">
        <div class="col-8">
            <h4>{{ strip_tags(__('pdf/user-report.pages.action-plan.advices.measure')) }}</h4>
        </div>
        <div class="col-4 text-right">
            <h4>@lang("{$package}.costs")</h4>
        </div>
    </div>
    @foreach($results->lines as $line)
        <div class="row">
            <div class="col-8">
                <p @if($line['package']) class="font-bold" @endif>{{ $line['name'] }}</p>
            </div>
            <div class="col-4 text-right">
                <p>{{ $costs($line['from'], $line['to']) }}</p>
            </div>
        </div>
    @endforeach
    <div class="row">
        <div class="col-8">
            <p class="font-bold">@lang("{$package}.total")</p>
        </div>
        <div class="col-4 text-right">
            <p class="font-bold">{{ $euro($results->total) }}</p>
        </div>
    </div>

    {{-- What SmartTwin calculated holds for the package as a whole, and only while the woonplan holds it. --}}
    <div class="py-2">
        @if($results->intact)
            @unless(is_null($results->savingsPerYear))
                <p class="font-bold">@lang("{$package}.savings"): {{ $euro($results->savingsPerYear) }} @lang("{$package}.per-year")</p>
            @endunless
            @unless(is_null($results->subsidy))
                <p class="font-bold">
                    @lang("{$package}.subsidy"): {{ $euro($results->subsidy) }}
                    @if(! empty($results->subsidies))
                        ({{ collect($results->subsidies)->pluck('name')->filter()->implode(', ') }})
                    @endif
                </p>
            @endunless
            @unless(is_null($results->labelAfter))
                <p>@lang("{$package}.energy-label"): {{ trim(($results->labelNow ? $results->labelNow . ' → ' : '') . $results->labelAfter) }}</p>
            @endunless
            <p>@lang("{$package}.gas"): {{ $nowAfter($results->gasNow, $results->gasAfter, 'm³') }} @lang("{$package}.per-year")</p>
            <p>@lang("{$package}.electricity"): {{ $nowAfter($results->electricityNow, $results->electricityAfter, 'kWh') }} @lang("{$package}.per-year")</p>
            @unless(is_null($results->co2Savings))
                <p>@lang("{$package}.co2-savings"): {{ $number($results->co2Savings) }} kg @lang("{$package}.per-year")</p>
            @endunless
        @else
            <p class="font-bold">@lang('pdf/user-report.pages.action-plan.smart-twin.broken')</p>
        @endif
    </div>
</div>

@if(! empty($woonplanReport->later))
    <div class="group">
        <h3>
            {{ strip_tags(__('pdf/user-report.pages.action-plan.categories.' . \App\Services\UserActionPlanAdviceService::CATEGORY_LATER)) }}
        </h3>
        @foreach($woonplanReport->later as $line)
            <div class="row">
                <div class="col-8">
                    <p>{{ $line['name'] }}</p>
                </div>
                <div class="col-4 text-right">
                    <p>{{ $costs($line['from'], $line['to']) }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if(! empty($woonplanReport->complete))
    <div class="group">
        <h3>
            {{ strip_tags(__('pdf/user-report.pages.action-plan.categories.' . \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE)) }}
        </h3>
        @foreach($woonplanReport->complete as $line)
            <div class="row">
                <div class="col-6">
                    <p>{{ $line['name'] }}</p>
                </div>
                <div class="col-6">
                    @if($line['executed_at'])
                        <p>
                            @lang('cooperation/frontend/tool.my-plan.executed.executed-on', ['date' => $line['executed_at']->format('d-m-Y')])
                            @if(! empty($line['description']))
                                <br>{{ $line['description'] }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
