<div class="flex items-center justify-between w-full bg-blue-100 border-b-2 border-blue border-opacity-20 h-16 px-5 xl:px-20 relative z-30">
    @foreach($steps as $step)
        @php
            $stepWasCompleted = $building->hasCompleted($step, \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT));
            $route = route('cooperation.frontend.tool.simple-scan.index', ['scan' => $scan, 'step' => $step, 'subStep' => $step->subSteps()->orderBy('order')->first()]);
            if ($stepWasCompleted) {
                $route = route('cooperation.frontend.tool.simple-scan.index', ['scan' => $scan, 'step' => $step, 'subStep' => $step->subSteps()->orderByDesc('order')->first()]);
            }
        @endphp
        <a href="{{ $route }}"
           class="no-underline">
            <div class="flex items-center">
                {{-- $currentStep gets injected in the SubNavComposer via the ViewServiceProvider --}}
                @if($stepWasCompleted)
                    <i class="icon-sm @if(isset($currentStep) && $currentStep->short == $step->short && ! isset($currentQuestionnaire)) icon-check-circle-purple @else icon-check-circle-dark @endif mr-1 border-purple"></i>
                @elseif(isset($currentStep) && $currentStep->short == $step->short && ! isset($currentQuestionnaire))
                    <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
                @else
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                @endif
                <span class="text-{{isset($currentStep) && $currentStep->short == $step->short && ! isset($currentQuestionnaire) ? 'purple' : 'blue'}}">
                    {{$step->name}}
                </span>
            </div>
        </a>

        @php
            // Eager loaded in SubNavComposer
            $hasQuestionnaires = $step->questionnaires->count() > 0;
        @endphp

        @if(! $loop->last || $hasQuestionnaires)
            <div class="step-divider-line"></div>
        @endif
        @if($hasQuestionnaires)
            @php
                // For styling, within the questionnaire, it might be a questionnaire within the set of steps, but could
                // also be at the end. We check this later
                $isLastStep = $loop->last;
            @endphp
            @foreach($step->questionnaires as $questionnaire)
                <a href="{{ route('cooperation.frontend.tool.simple-scan.questionnaires.index', compact('scan', 'step', 'questionnaire')) }}"
                   class="no-underline">
                    <div class="flex items-center">
                        {{-- $currentQuestionnaire gets injected in the SubNavComposer via the ViewServiceProvider --}}
                        @if($building->user->hasCompletedQuestionnaire($questionnaire, \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT)))
                            <i class="icon-sm @if(isset($currentQuestionnaire) && $currentQuestionnaire->id == $questionnaire->id) icon-check-circle-purple @else icon-check-circle-dark @endif mr-1 border-purple"></i>
                        @elseif(isset($currentQuestionnaire) && $currentQuestionnaire->id == $questionnaire->id)
                            <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
                        @else
                            <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                        @endif
                        <span class="text-{{isset($currentQuestionnaire) && $currentQuestionnaire->id == $questionnaire->id ? 'purple' : 'blue'}}">
                            {{$questionnaire->name}}
                        </span>
                    </div>
                </a>

                @if(! $loop->last || ! $isLastStep)
                    <div class="step-divider-line"></div>
                @endif
            @endforeach
        @endif
    @endforeach
    @php
        $inMyPlan = RouteLogic::inMyPlan(Route::currentRouteName());
        $canAccessWoonplan = \App\Services\WoonplanService::init($building)
            ->scan($scan)
            ->canAccessWoonplan();
        $color = 'blue';
        if ($canAccessWoonplan) {
            $color = 'green';
        }
        if ($inMyPlan)  {
          $color = 'purple';
        }

        $iconColor = $color === 'blue' ? 'dark' : $color;
    @endphp
    <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
    <a href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')) }}" class="no-underline">
        <div class="flex items-center justify-start">
            <i class="icon-sm {{ 'icon-house-' . $iconColor }} mr-1"></i>
            <span class="{{ 'text-' . $color }}">
                @lang('cooperation/frontend/tool.my-plan.label')
            </span>
        </div>
    </a>
</div>
