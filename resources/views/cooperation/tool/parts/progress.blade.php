<ul class="progress-list list-inline">
<?php
    $building = \App\Helpers\HoomdossierSession::getBuilding(true);

        // we get the building-detail and general-data
        $generalDataStep = $steps->where('slug', 'general-data')->first();

        $inputSource = \App\Helpers\HoomdossierSession::getInputSource(true);
    ?>

    {{--
        We treat / show building-detail and general-data as 1 step. We "merge" them to 1 and use tabs
        to make it look like it is 1 step.
    --}}
    @foreach($steps as $step)
        <?php
            $userDoesNotHaveInterestInStep = !\App\Helpers\StepHelper::hasInterestInStep($building, $inputSource, $step);
            $routeIsCurrentRoute = Route::is('cooperation.tool.' . $step->slug . '.index');
        ?>
        {{-- There is no interest for the general-data so we skip that --}}
        <li class="list-inline-item @if($routeIsCurrentRoute) active @elseif($building->hasCompleted($step)) done @endif @if($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep) not-available @endif">
            <a href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/icons/' . $step->slug . '.png') }}" title="{{ $step->name }}@if($step->short != 'general-data' && $userDoesNotHaveInterestInStep) - @lang('default.progress.disabled')@endif" alt="{{ $step->name }}" class="img-circle"/>
            </a>
        </li>
    @endforeach
    <li class="list-inline-item">
        <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
            <img src="{{ asset('images/icons/my-plan.png') }}" class="img-circle" />
        </a>
    </li>
</ul>