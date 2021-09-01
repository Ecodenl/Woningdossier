<div class="flex items-center justify-between w-full bg-blue-100 border-b-2 border-blue border-opacity-20 h-16 px-5 xl:px-20 relative z-30">
    @foreach($steps as $step)
        <a href="{{ route('cooperation.frontend.tool.quick-scan.index', ['step' => $step, 'subStep' => $step->subSteps()->orderBy('order')->first()]) }}"
           class="no-underline">
            <div class="flex items-center">
                {{-- $currentStep gets injected in the SubNavComposer via the ViewServiceProvider --}}
                @if($building->hasCompleted($step, \App\Helpers\HoomdossierSession::getInputSource(true)))
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
            $questionnaires = $step->questionnaires()->orderBy('order')->active()->get();
            $hasQuestionnaires = $questionnaires->count() > 0;
        @endphp

        @if(! $loop->last || $hasQuestionnaires)
            <div class="step-divider-line"></div>
        @endif
        @if($hasQuestionnaires)
            @foreach($questionnaires as $questionnaire)
                <a href="{{ route('cooperation.frontend.tool.quick-scan.questionnaires.index', compact('step', 'questionnaire')) }}"
                   class="no-underline">
                    <div class="flex items-center">
                        {{-- $currentQuestionnaire gets injected in the SubNavComposer via the ViewServiceProvider --}}
                        @if($building->user->hasCompletedQuestionnaire($questionnaire, \App\Helpers\HoomdossierSession::getInputSource(true)))
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

                @if(! $loop->last)
                    <div class="step-divider-line"></div>
                @endif
            @endforeach
        @endif
    @endforeach
    @php $inMyPlan = \App\Helpers\Blade\RouteLogic::inMyPlan(Route::currentRouteName()); @endphp
    <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
    <a href="{{ route('cooperation.frontend.tool.quick-scan.my-plan.index') }}" class="no-underline">
        <div class="flex items-center justify-start">
            <i class="icon-sm {{ $inMyPlan ? 'icon-house-purple' : 'icon-house-dark' }} mr-1"></i>
            <span class="{{ $inMyPlan ? 'text-purple' : 'text-blue' }}">
                @lang('cooperation/frontend/tool.my-plan.label')
            </span>
        </div>
    </a>
</div>
