<div class="w-1/2 flex flex-wrap justify-center">
    @php
        $previousUrl = null;
        $nextUrl = null;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $previousStep->scan, 'step' => $previousStep, 'subStep' => $previousSubStep]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['scan' => $previousStep->scan, 'step' => $previousStep, 'questionnaire' => $previousQuestionnaire]);
        }
        if ($nextStep instanceof \App\Models\Step && $nextSubStep instanceof \App\Models\SubStep) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $nextStep->scan, 'step' => $nextStep, 'subStep' => $nextSubStep]);
        } elseif ($nextStep instanceof \App\Models\Step && $nextQuestionnaire instanceof \App\Models\Questionnaire) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['scan' => $nextStep->scan, 'step' => $nextStep, 'questionnaire' => $nextQuestionnaire]);
        } else {
            if ($firstIncompleteStep instanceof \App\Models\Step && $firstIncompleteSubStep instanceof \App\Models\SubStep) {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['scan' => $firstIncompleteStep->scan, 'step' => $firstIncompleteStep, 'subStep' => $firstIncompleteSubStep]);
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index', ['scan' => $currentScan]);
            }
        }

    @endphp

    @if($previousStep instanceof \App\Models\Step || $previousSubStep instanceof \App\Models\SubStep)
        <a href="{{$previousUrl}}" class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('default.buttons.previous')
        </a>
    @endif


    <button type="button" x-data
            {{-- Because a questionnaire is simply saved using an old school controller, we update the action, which
            will handle redirecting us correctly to the questionnaire... TODO: Refactor this, no time for it now --}}
            @if(RouteLogic::inQuestionnaire(Route::currentRouteName()))
            x-on:click="$el.setAttribute('disabled', true); let form = document.querySelector('#questionnaire-form-{{$questionnaire->id}}'); let action = form.getAttribute('action'); action += '?nextUrl={{$nextUrl}}'; form.setAttribute('action', action); form.submit();"
            @else
            wire:click="$emitTo('cooperation.frontend.tool.quick-scan.form', 'save', '{{$nextUrl}}')"
            x-on:click="$el.setAttribute('disabled', true);"
            @endif
            x-on:validation-failed.window="$el.removeAttribute('disabled');"
            class="btn btn-purple flex items-center ml-1">
        @lang('default.buttons.next')
        <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
    </button>
</div>
