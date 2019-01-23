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
                'ventilation-information' => [
                    '6',
                ],
            ],
        ];

        $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());

    ?>
    @foreach($steps as $step)
        <li class="list-inline-item
            @if($step->slug == "building-detail")
                {{--active--}}
            @else


            @if(Route::currentRouteName() == 'cooperation.tool.' . $step->slug . '.index')
                active
            @elseif($building->hasCompleted($step))
                done
            @endif

            @if($step->slug != "general-data" && Route::currentRouteName() != 'cooperation.tool.' . $step->slug . '.index')
                @foreach ($stepInterests as $interestedInType => $interestedInNames)
                    @foreach ($interestedInNames as $interestedInName => $interestedInIds)
                        @if ($interestedInName == $step->slug && $building->isNotInterestedInStep($interestedInType, $interestedInIds) == true)
                          not-available
                        @endif
                    @endforeach
                @endforeach
            @endif ">

            <a
            @if($step->slug == ("general-data" || "building-detail"))
                href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle"/>
            @else
                @foreach ($stepInterests as $interestedInType => $interestedInNames)
                    @foreach ($interestedInNames as $interestedInName => $interestedInIds)

                        @if ($interestedInName == $step->slug)
                            href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                            <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}@if($building->isNotInterestedInStep($interestedInType, $interestedInIds)) - @lang('default.progress.disabled')@endif" alt="{{ $step->name }}" class="img-circle"/>
                        @endif

                    @endforeach
                @endforeach
            @endif
            </a>
        </li>
            @endif
    @endforeach
        <li class="list-inline-item">
            <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/my-plan.png') }}" class="img-circle" />
            </a>
        </li>
</ul>