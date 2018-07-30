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
                '2'
            ],
            'floor-insulation' => [
                '4'
            ],
            'roof-insulation' => [
                '5'
            ],
        ],
        'service' => [
            'high-efficiency-boiler' => [
                '4'
            ],
            'heat-pump' => [
                '1',
                '2'
            ],
            'solar-panels' => [
                '7'
            ],
            'heater' => [
                '3'
            ],
            'ventilation-information' => [
                '6'
            ],
        ]
    ];

    ?>
    @foreach($steps as $step)
            {{--        @if($step->slug == 'heat-pump' || $step->slug == 'heat-pump-information')--}}
            {{--<li class="list-inline-item">--}}
                    {{--<a href="{{route('cooperation.tool.'.$step->slug.'.index')}}">--}}
                            {{--<img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle"/>--}}
                    {{--</a>--}}
            {{--</li>--}}
            {{--@else--}}
            {{--@endif--}}

        <li class="list-inline-item @if(Auth::user()->hasCompleted($step)) done @elseif(Route::currentRouteName() == 'cooperation.tool.' . $step->slug . '.index') active @endif

            @if($step->slug != "general-data")
                @foreach ($stepInterests as $interestedInType => $interestedInNames)
                    @foreach ($interestedInNames as $interestedInName => $interestedInIds)
                        @if ($interestedInName == $step->slug && Auth::user()->isNotInterestedInStep($interestedInType, $interestedInIds) == true)
                          not-available
                        @endif
                    @endforeach
                @endforeach
            @endif ">
            <a
                @if($step->slug == "general-data")
                href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle"/>
                @else
                    @foreach ($stepInterests as $interestedInType => $interestedInNames)
                        @foreach ($interestedInNames as $interestedInName => $interestedInIds)

                            @if ($interestedInName == $step->slug && Auth::user()->isNotInterestedInStep($interestedInType, $interestedInIds) == false)
                                href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                                <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle"/>
                            @endif

                            @if ($interestedInName == $step->slug && Auth::user()->isNotInterestedInStep($interestedInType, $interestedInIds) == true)
                                href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                                <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }} - @lang('default.progress.disabled')" alt="{{ $step->name }}" class="img-circle"/>
                            @endif

                        @endforeach
                    @endforeach
                @endif
            </a>
        </li>

    @endforeach
        <li class="list-inline-item">
            <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/my-plan.png') }}" class="img-circle" />
            </a>
        </li>
</ul>

{{--
    @for($i = 0; $i < 9; $i++)
    <li class="list-inline-item @if($i < 3)done @elseif($i == 3)active @endif"><a href="#"><img src="http://placekitten.com/g/50/50" class="img-circle"></a></li>
    @endfor
--}}