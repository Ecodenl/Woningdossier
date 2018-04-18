<ul class="progress-list list-inline">
@foreach($steps as $step)
        @if($step->slug == 'heat-pump')
        <li class="list-inline-item">
                <a href="#">
                    <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle" style="opacity: 0.3;"/>
                </a>
        </li>
        @else
        <li class="list-inline-item @if(Auth::user()->progress()->where('step_id', $step->id)->count() > 0) done @elseif(Route::currentRouteName() == 'cooperation.tool.' . $step->slug . '.index') active @endif">
                <a href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                        <img src="{{ asset('images/' . $step->slug . '.png') }}" title="{{ $step->name }}" alt="{{ $step->name }}" class="img-circle"/>
                </a>
        </li>
        @endif
@endforeach
        <li class="list-inline-item">
                <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
                        <img src="http://placekitten.com/g/50/50" class="img-circle" />
                </a>
        </li>
</ul>

{{--
    @for($i = 0; $i < 9; $i++)
    <li class="list-inline-item @if($i < 3)done @elseif($i == 3)active @endif"><a href="#"><img src="http://placekitten.com/g/50/50" class="img-circle"></a></li>
    @endfor
--}}