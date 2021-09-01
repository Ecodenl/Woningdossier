
<div class="w-1/2 flex flex-wrap justify-center">
    @php
        $previousUrl = null;
        $nextUrl = null;

        if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $previousStep, 'subStep' => $previousSubStep]);
        } elseif ($previousStep instanceof \App\Models\Step && $previousQuestionnaire instanceof \App\Models\Questionnaire) {
            $previousUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['step' => $previousStep, 'questionnaire' => $previousQuestionnaire]);
        }
        if ($nextStep instanceof \App\Models\Step && $nextSubStep instanceof \App\Models\SubStep) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $nextStep, 'subStep' => $nextSubStep]);
        } elseif ($nextStep instanceof \App\Models\Step && $nextQuestionnaire instanceof \App\Models\Questionnaire) {
            $nextUrl = route('cooperation.frontend.tool.quick-scan.questionnaires.index', ['step' => $nextStep, 'questionnaire' => $nextQuestionnaire]);
        } else {
            if ($firstIncompleteStep instanceof \App\Models\Step && $firstIncompleteSubStep instanceof \App\Models\SubStep) {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $firstIncompleteStep, 'subStep' => $firstIncompleteSubStep]);
            } else {
                $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index');
            }
        }
    @endphp

    @if($previousStep instanceof \App\Models\Step || $previousSubStep instanceof \App\Models\SubStep)
        <a href="{{$previousUrl}}" class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('cooperation/frontend/shared.defaults.previous')
        </a>
    @endif

    <button type="button" wire:click="$emitTo('cooperation.frontend.tool.quick-scan.form', 'save', '{{$nextUrl}}')"
            class="btn btn-purple flex items-center ml-1">
        @lang('cooperation/frontend/shared.defaults.next')
        <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
    </button>
</div>
