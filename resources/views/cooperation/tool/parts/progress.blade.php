<ul class="progress-list list-inline">
    <?php
    $building = \App\Helpers\HoomdossierSession::getBuilding(true);
    $currentStepIsSubStep = $currentSubStep instanceof \App\Models\Step;

    $generalDataStep = $steps->where('slug', 'general-data')->first();

    $inputSource = \App\Helpers\HoomdossierSession::getInputSource(true);
    ?>

    @foreach($steps as $step)
        <?php
        $userDoesNotHaveInterestInStep = false;
        if ($step->short != 'general-data') {
            $userDoesNotHaveInterestInStep = !\App\Helpers\StepHelper::hasInterestInStep($buildingOwner, get_class($step), $step->id);
        }
        $currentRouteName = $currentStepIsSubStep ? 'cooperation.tool.' . $step->short . '.' . $currentSubStep->short . '.index' : 'cooperation.tool.' . $step->short . '.index';
        $routeIsCurrentRoute = $currentRouteName == Route::getCurrentRoute()->getName();
        $isStepCompleted = $building->hasCompleted($step);

        if ($isStepCompleted) {
            $titleForIcon = __('default.progress.completed', ['step' => $step->name]);
        } elseif ($userDoesNotHaveInterestInStep) {
            $titleForIcon = __('default.progress.disabled', ['step' => $step->name]);
        } else {
            $titleForIcon = __('default.progress.not-completed', ['step' => $step->name]);
        }

        ?>
        {{-- There is no interest for the general-data so we skip that --}}
        <li class="list-inline-item
            @if($routeIsCurrentRoute)
                active
            @elseif($isStepCompleted)
                done
            @endif
        @if($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep)
                not-available
            @endif
                ">
            <a href="{{ route('cooperation.tool.' . $step->slug . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/icons/' . $step->slug . '.png') }}"
                     title="{{$titleForIcon}}"
                     alt="{{ $step->name }}" class="img-circle"/>
            </a>
            @if(!$routeIsCurrentRoute)
                @if($isStepCompleted)
                    <span class="glyphicon glyphicon-ok"></span>
                @elseif($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep)
                    <span class="glyphicon glyphicon-ban-circle"></span>
                @endif
            @endif

        </li>
    @endforeach

    <li class="list-inline-item">
        <a class="no-border" href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
            <img class="no-border" src="{{ asset('images/icons/my-plan.png') }}"/>
        </a>
    </li>
</ul>