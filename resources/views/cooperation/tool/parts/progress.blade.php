<ul class="progress-list list-none -ml-1 flex flex-row flex-wrap w-full justify-center mt-5">
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

        if ($isStepCompleted && !$userDoesNotHaveInterestInStep) {
            $titleForIcon = __('default.progress.completed', ['step' => $step->name]);
        } elseif ($userDoesNotHaveInterestInStep) {
            $titleForIcon = __('default.progress.disabled', ['step' => $step->name]);
        } else {
            $titleForIcon = __('default.progress.not-completed', ['step' => $step->name]);
        }

        ?>
        {{-- There is no interest for the general-data so we skip that --}}
        <li class="inline-block px-1 @if($routeIsCurrentRoute) active @elseif($isStepCompleted) done @endif
            @if($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep) not-available @endif">
            <a href="{{ route('cooperation.tool.' . $step->short . '.index', ['cooperation' => $cooperation]) }}">
                <img src="{{ asset('images/icons/' . $step->slug . '.png') }}"
                     title="{{$titleForIcon}}"
                     alt="{{ $step->name }}" class="rounded-1/2"/>
            </a>
            @if(!$routeIsCurrentRoute)
                @if($isStepCompleted && !$userDoesNotHaveInterestInStep)
                    <i class="icon-md icon-check-circle-green"></i>
                @elseif($step->short != 'general-data' && !$routeIsCurrentRoute && $userDoesNotHaveInterestInStep)
                    <i class="icon-md icon-error-cross"></i>
                @endif
            @endif

        </li>
    @endforeach

    <li class="inline-block px-1">
        <a class="no-border" href="{{ route('cooperation.tool.my-plan.index', ['cooperation' => $cooperation]) }}">
            <img class="no-border" src="{{ asset('images/icons/my-plan.png') }}"/>
        </a>
    </li>
</ul>