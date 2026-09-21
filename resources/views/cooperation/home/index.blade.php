{{-- With SmartTwin off this route is the old start screen and hangs off the bare layout, as it
     always has. With it on it is the dashboard, and needs the frontend shell around it. --}}
@extends(Hoomdossier::hasEnabledSmartTwinCalls() ? 'cooperation.frontend.layouts.frontend' : 'cooperation.layouts.app')

@if(Hoomdossier::hasEnabledSmartTwinCalls())
    {{-- The dashboard is a page of tiles, not a card floating on a photo. --}}
    @section('main_style', '')
@endif

@section('main')
    @if(Hoomdossier::hasEnabledSmartTwinCalls())
        <div class="dashboard">
            @if(session('verified'))
                <div class="dashboard-full">
                    @component('cooperation.layouts.components.alert', ['color' => 'blue-900'])
                        @lang('cooperation/auth/verify.success-log-in')
                    @endcomponent
                </div>
            @endif

            {{-- The house -------------------------------------------------------------------- --}}
            <section class="tile dashboard-main">
                <h3 class="tile-heading">
                    @lang('home.dashboard.building.title')
                </h3>

                <div class="dashboard-building">
                    <livewire:cooperation.frontend.dashboard.building-image :building="$building"/>

                    <div class="building-facts">
                        <p class="building-facts-intro">
                            @lang('home.dashboard.building.source')
                        </p>

                        <dl>
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-placeholder',
                                'label' => __('home.dashboard.building.address'),
                                'value' => $building->street
                                    ? "{$building->street} {$building->number}{$building->extension}, {$building->city}"
                                    : null,
                            ])
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-house-dark',
                                'label' => __('home.dashboard.building.type'),
                                'value' => $features?->buildingType?->name,
                            ])
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-calendar',
                                'label' => __('home.dashboard.building.build-year'),
                                'value' => $features?->build_year,
                            ])
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-other',
                                'label' => __('home.dashboard.building.surface'),
                                {{-- The column is a decimal, so it arrives as "139.00" without this. --}}
                                'value' => $features?->surface
                                    ? \App\Helpers\NumberFormatter::format($features->surface, 0) . ' m²'
                                    : null,
                            ])
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-sustainability',
                                'label' => __('home.dashboard.building.energy-label.official'),
                                'energyLabel' => $features?->energyLabel,
                            ])
                            {{-- The estimated label comes from SmartTwin later; null until it does. --}}
                            @include('cooperation.frontend.dashboard.parts.fact', [
                                'icon' => 'icon-sustainability',
                                'label' => __('home.dashboard.building.energy-label.estimated'),
                                'energyLabel' => $estimatedEnergyLabel,
                            ])
                        </dl>
                    </div>
                </div>

                <div class="tile-action">
                    <a class="btn btn-blue" href="{{ $dossierUrl }}">
                        @lang('home.dashboard.building.to-dossier')
                    </a>
                </div>
            </section>

            {{-- The coach -------------------------------------------------------------------- --}}
            <section class="tile">
                <h3 class="tile-heading">
                    @lang('home.dashboard.coach.title')
                </h3>

                @if($coach instanceof \App\Models\User)
                    <p>
                        <strong>@lang('home.dashboard.coach.name')</strong><br>
                        {{ $coach->getFullName() }}
                    </p>
                @else
                    <p>@lang('home.dashboard.coach.none')</p>
                @endif

                @if(! is_null($appointmentDate))
                    <p>
                        <strong>@lang('home.dashboard.coach.appointment')</strong><br>
                        {{ $appointmentDate->translatedFormat('j F Y \o\m H.i \u\u\r') }}
                    </p>
                @else
                    <p>@lang('home.dashboard.coach.no-appointment')</p>
                @endif

                <div class="tile-action">
                    <a class="btn btn-blue" href="{{ route('cooperation.my-account.messages.edit') }}">
                        @lang('home.dashboard.coach.contact')
                    </a>
                </div>
            </section>

            {{-- The files -------------------------------------------------------------------- --}}
            @can('viewAny', [\App\Models\Media::class, $inputSource, $building])
                <section class="tile">
                    <h3 class="tile-heading">
                        @lang('home.dashboard.files.title')
                    </h3>

                    <a href="{{ $filesUrl }}" class="dashboard-files no-underline">
                        <i class="icon-xxxl icon-document"></i>
                    </a>
                </section>
            @endcan
        </div>
    @else
        <div class="w-screen h-screen flex justify-center items-center flex-col">
            <div class="bg-white rounded-3xl w-3/4 flex flex-wrap overflow-hidden min-h-15/20 max-h-19/20">
                <div class="p-10 xl:p-20 w-1/2 flex flex-col justify-between h-full overflow-auto">
                    @if(session('verified'))
                        @component('cooperation.layouts.components.alert', ['color' => 'blue-900'])
                            @lang('cooperation/auth/verify.success-log-in')
                        @endcomponent
                    @endif
                    {!! __('home.start.description') !!}


                    <div class="flex justify-between space-x-2">
                        @foreach($scans as $scan)
                            @php
                                $transShort = app(\App\Services\Models\ScanService::class)
                                    ->scan($scan)->forBuilding($building)->hasMadeScanProgress()
                                    ? 'home.start.buttons.continue' : 'home.start.buttons.start';
                            @endphp
                            <a class="btn btn-purple"
                               href="{{\App\Services\Scans\ScanFlowService::init($scan, $building, $inputSource)->resolveInitialUrl()}}">
                                @lang($transShort, ['scan' => $scan->name])
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="text-center w-1/2 relative bg-center bg-no-repeat bg-cover"
                     style="background-image: url('{{ asset('images/family.png') }}')">
                    <i class="icon-hoomdossier-white absolute h-1/10 w-1/2 bottom-1/20" style="right: 12%"></i>
                </div>
            </div>
        </div>
    @endif
@endsection
