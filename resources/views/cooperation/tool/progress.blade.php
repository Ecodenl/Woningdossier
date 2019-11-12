<ul class="progress-list list-inline">

    <?php
        $stepInterests = [
            // interested in type
            'element' => [
                // step slug
                'wall-insulation' => [
                    // interested in id
                    '3',
                ],
                'insulated-glazing' => [
                    '1',
                ],
                'floor-insulation' => [
                    '4',
                ],
                'roof-insulation' => [
                    '5',
                ],
            ],
            'service' => [
                'high-efficiency-boiler' => [
                    '4',
                ],
                'heat-pump' => [
                    '1',
                    '2',
                ],
                'solar-panels' => [
                    '7',
                ],
                'heater' => [
                    '3',
                ],
                'ventilation' => [
                    '6',
                ],
            ],
        ];

        $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());

        // we get the building-detail and general-data
        $buildingDetailStep = $steps->where('slug', 'building-detail')->first();
        $generalDataStep = $steps->where('slug', 'general-data')->first();

    ?>

    {{--
        We treat / show building-detail and general-data as 1 step. We "merge" them to 1 and use tabs
        to make it look like it is 1 step.
    --}}
    <li class="list-inline-item
        @if(in_array(Route::currentRouteName(), ['cooperation.tool.building-detail.index', 'cooperation.tool.general-data.index']))
            active
        @elseif ($building->hasCompleted($buildingDetailStep) && $building->hasCompleted($generalDataStep))
            done
        @endif
        ">
        <?php $houseIconLink = $building->hasCompleted($buildingDetailStep) ? route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]) : route('cooperation.tool.building-detail.index', ['cooperation' => $cooperation]); ?>
        <a href="{{$houseIconLink}}">
            <img src="{{ asset('images/icons/building-detail.png') }}" title="" alt="@lang('woningdossier.cooperation.step.building-detail')" class="img-circle"/>
        </a>
    </li>
    @foreach($steps as $step)
        {{--
            The general-data and building detail are 2 steps that we treat as 1.
            So we cant place them with a loop
        --}}
        @if(!in_array($step->slug, ['general-data', 'building-detail']))
            <li class="list-inline-item
                @if(Route::currentRouteName() == 'cooperation.tool.' . $step->slug . '.index')
                    active
                @elseif($building->hasCompleted($step))
                    done
                @endif

                @if(Route::currentRouteName() != 'cooperation.tool.' . $step->slug . '.index')
                    @foreach ($stepInterests as $interestedInType => $interestedInNames)
                        @foreach ($interestedInNames as $interestedInName => $interestedInIds)
                            @if ($interestedInName == $step->slug && $building->isNotInterestedInStep(\App\Helpers\HoomdossierSession::getInputSource(true), $interestedInType, $interestedInIds) == true)
                              not-available
                            @endif
                        @endforeach
                    @endforeach
                @endif ">

                <a
                    @foreach ($stepInterests as $interestedInType => $interestedInNames)
                        @foreach ($interestedInNames as $interestedInName => $interestedInIds)

                            @if ($interestedInName == $step->slug)
                                href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                                <img src="{{ asset('images/icons/' . $step->slug . '.png') }}" title="{{ $step->name }}@if($building->isNotInterestedInStep(\App\Helpers\HoomdossierSession::getInputSource(true), $interestedInType, $interestedInIds)) - @lang('default.progress.disabled')@endif" alt="{{ $step->name }}" class="img-circle"/>
                            @endif

                        @endforeach
                    @endforeach
                </a>
            </li>
        @endif
    @endforeach
    <li class="list-inline-item">
        <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
            <img src="{{ asset('images/icons/my-plan.png') }}" class="img-circle" />
        </a>
    </li>

</ul>