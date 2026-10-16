{{--
    The dwelling in the report once the woonplan is SmartTwin's: the same as "Gegevens woning bekijken",
    in place of the summaries of the steps SmartTwin takes over (decision
    2026-10-15-smarttwin-rapport-na-omslagpunt, P5). See App\Services\SmartTwin\HouseData.
--}}
@php
    /** @var \App\Services\SmartTwin\HouseData $houseData */
    $features = $houseData->features;
    $houseDataLang = 'cooperation/frontend/tool.my-plan.house-data';
    $unknown = __('home.dashboard.building.unknown');
@endphp

<div class="group">
    <h3>@lang("{$houseDataLang}.general")</h3>
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => __('home.dashboard.building.type'),
        'answer' => e($features?->buildingType?->name ?? $features?->buildingTypeCategory?->name ?? $unknown),
    ])
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => __('home.dashboard.building.build-year'),
        'answer' => e($features?->build_year ?? $unknown),
    ])
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => __('home.dashboard.building.surface'),
        'answer' => $features?->surface ? \App\Helpers\NumberFormatter::format($features->surface, 0) . ' m²' : e($unknown),
    ])
    @include('cooperation.pdf.user-report.parts.smart-twin.house-data-rows', ['rows' => $houseData->general])
</div>

<div class="group">
    <h3>@lang("{$houseDataLang}.energy-label")</h3>
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => __('home.dashboard.building.energy-label.official'),
        'answer' => e($features?->energyLabel?->name ?? $unknown),
    ])
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => __('home.dashboard.building.energy-label.estimated'),
        'answer' => e($houseData->estimatedEnergyLabel ?? $unknown),
    ])
</div>

<div class="group">
    <h3>@lang("{$houseDataLang}.state")</h3>
    @foreach($houseData->parts as $part => $data)
        <h4>@lang("{$houseDataLang}.parts.{$part}")</h4>
        {{-- What the resident carried out goes above SmartTwin's information. --}}
        @foreach($data['executed'] as $executed)
            <p>
                @lang("{$houseDataLang}.executed", ['measure' => $executed['measure'], 'date' => $executed['date']->format('d-m-Y')])
                @if(! empty($executed['description']))
                    &ndash; {{ $executed['description'] }}
                @endif
            </p>
        @endforeach
        @if(empty($data['rows']))
            <p>@lang("{$houseDataLang}.nothing-known")</p>
        @else
            @if($houseData->calculatedAt)
                <p>@lang("{$houseDataLang}.according-to", ['date' => $houseData->calculatedAt->format('d-m-Y')])</p>
            @endif
            @include('cooperation.pdf.user-report.parts.smart-twin.house-data-rows', ['rows' => $data['rows']])
        @endif
    @endforeach
</div>

{{-- SmartTwin does not deliver usage: only what the dossier holds, if anything. --}}
@if(! empty($houseData->usage))
    <div class="group">
        <h3>@lang("{$houseDataLang}.usage")</h3>
        @include('cooperation.pdf.user-report.parts.smart-twin.house-data-rows', ['rows' => $houseData->usage])
    </div>
@endif
