{{--
    "Gegevens woning bekijken": the dwelling as it is now, after SmartTwin's last import. General,
    energy label, state and usage, as the process design names them. See App\Services\SmartTwin\HouseData.
--}}
@extends('cooperation.frontend.layouts.tool')

@section('content')
    @php
        $features = $houseData->features;
        $date = fn (\Illuminate\Support\Carbon $date) => $date->translatedFormat('j F Y');
    @endphp

    <div class="w-full space-y-8">
        <div class="w-full flex flex-wrap justify-between items-center">
            <h4 class="heading-4">@lang('cooperation/frontend/tool.my-plan.house-data.title')</h4>
            <a href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')) }}" class="btn btn-outline-purple">
                @lang('cooperation/frontend/tool.my-plan.house-data.back')
            </a>
        </div>
        <p>@lang('cooperation/frontend/tool.my-plan.house-data.intro')</p>

        <div class="w-full flex flex-wrap -mx-3">
            <div class="w-full md:w-1/2 px-3">
                <h5 class="heading-5 mb-2">@lang('cooperation/frontend/tool.my-plan.house-data.general')</h5>
                <dl class="building-facts">
                    @include('cooperation.frontend.dashboard.parts.fact', [
                        'icon' => 'icon-house-dark',
                        'label' => __('home.dashboard.building.type'),
                        'value' => $features?->buildingType?->name ?? $features?->buildingTypeCategory?->name,
                    ])
                    @include('cooperation.frontend.dashboard.parts.fact', [
                        'icon' => 'icon-calendar',
                        'label' => __('home.dashboard.building.build-year'),
                        'value' => $features?->build_year,
                    ])
                    @include('cooperation.frontend.dashboard.parts.fact', [
                        'icon' => 'icon-other',
                        'label' => __('home.dashboard.building.surface'),
                        'value' => $features?->surface
                            ? \App\Helpers\NumberFormatter::format($features->surface, 0) . ' m²'
                            : null,
                    ])
                </dl>
            </div>

            <div class="w-full md:w-1/2 px-3">
                <h5 class="heading-5 mb-2">@lang('cooperation/frontend/tool.my-plan.house-data.energy-label')</h5>
                <dl class="building-facts">
                    @include('cooperation.frontend.dashboard.parts.fact', [
                        'icon' => 'icon-sustainability',
                        'label' => __('home.dashboard.building.energy-label.official'),
                        'energyLabel' => $features?->energyLabel,
                    ])
                    @include('cooperation.frontend.dashboard.parts.fact', [
                        'icon' => 'icon-sustainability',
                        'label' => __('home.dashboard.building.energy-label.estimated'),
                        'energyLabel' => $houseData->estimatedEnergyLabel,
                    ])
                </dl>
            </div>
        </div>

        <div class="w-full">
            <h5 class="heading-5 mb-2">@lang('cooperation/frontend/tool.my-plan.house-data.state')</h5>
            @foreach($houseData->parts as $part => $data)
                <div class="w-full mb-6" id="house-data-{{ $part }}">
                    <h6 class="heading-6 font-bold mb-2">@lang("cooperation/frontend/tool.my-plan.house-data.parts.{$part}")</h6>

                    {{-- What the resident carried out goes above SmartTwin's information (S6d). --}}
                    @foreach($data['executed'] as $executed)
                        <p class="flex items-center mb-1 text-green">
                            <i class="icon-sm icon-check-circle-dark mr-2"></i>
                            <span>
                                @lang('cooperation/frontend/tool.my-plan.house-data.executed', ['measure' => $executed['measure'], 'date' => $date($executed['date'])])
                                @if(! empty($executed['description']))
                                    &ndash; {{ $executed['description'] }}
                                @endif
                            </span>
                        </p>
                    @endforeach

                    @if(empty($data['rows']))
                        <p class="text-blue-500">@lang('cooperation/frontend/tool.my-plan.house-data.nothing-known')</p>
                    @else
                        @if($houseData->calculatedAt)
                            <p class="text-sm text-blue-500 mb-1">
                                @lang('cooperation/frontend/tool.my-plan.house-data.according-to', ['date' => $date($houseData->calculatedAt)])
                            </p>
                        @endif
                        @include('cooperation.frontend.tool.simple-scan.my-plan.parts.house-data-rows', ['rows' => $data['rows']])
                    @endif
                </div>
            @endforeach
        </div>

        {{-- SmartTwin does not deliver usage: only what the dossier holds, if anything (B7). --}}
        @if(! empty($houseData->usage))
            <div class="w-full">
                <h5 class="heading-5 mb-2">@lang('cooperation/frontend/tool.my-plan.house-data.usage')</h5>
                @include('cooperation.frontend.tool.simple-scan.my-plan.parts.house-data-rows', ['rows' => $houseData->usage])
            </div>
        @endif
    </div>
@endsection
