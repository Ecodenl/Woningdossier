<ul class="progress-list list-inline">
    <?php
        $building = \App\Helpers\HoomdossierSession::getBuilding(true);
        $currentStepIsSubStep = $currentSubStep instanceof \App\Models\Step;
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
        $userDoesNotHaveInterestInStep = false;
        if ($step->short != 'general-data') {
            $userDoesNotHaveInterestInStep = !\App\Helpers\StepHelper::hasInterestInStep($buildingOwner, get_class($step), $step->id);
        }
        $currentRouteName = $currentStepIsSubStep ? 'cooperation.tool.' . $step->short . '.' . $currentSubStep->short . '.index' : 'cooperation.tool.' . $step->short . '.index';
        $routeIsCurrentRoute = $currentRouteName == Route::getCurrentRoute()->getName();
        ?>
        {{-- There is no interest for the general-data so we skip that --}}
        <li class="list-inline-item
            @if($routeIsCurrentRoute)
                active
            @elseif($building->hasCompleted($step))
                done
            @endif
            @if($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep)
                not-available
            @endif
                ">
            <a href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/icons/' . $step->slug . '.png') }}"
                     title="{{ $step->name }}@if($step->short != 'general-data' && $userDoesNotHaveInterestInStep) - @lang('default.progress.disabled')@endif"
                     alt="{{ $step->name }}" class="img-circle"/>
            </a>
            @if(!$routeIsCurrentRoute)
                @if($building->hasCompleted($step))
                    <span class="glyphicon glyphicon-ok"></span>
                @elseif($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep)
                    <span class="glyphicon glyphicon-ban-circle"></span>
                @endif
            @endif

        </li>
    @endforeach

    <li class="list-inline-item">
        <a href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
            <img class="no-border" src="{{ asset('images/icons/my-plan.png') }}"/>
        </a>
    </li>
</ul>