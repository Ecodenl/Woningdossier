
<div class="w-1/2 flex flex-wrap justify-center">
<?php
    $previousUrl = null;
    $nextUrl = null;

    $previousStepKey = optional($previousStep)->getRouteKey();
    $nextStepKey = optional($nextStep)->getRouteKey();

    $previousSubStepKey = optional($previousSubStep)->slug;
    $nextSubStepKey = optional($nextSubStep)->slug;

    if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
        $previousUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $previousStepKey, 'subStep' => $previousSubStepKey]);
    }
    if ($nextStep instanceof \App\Models\Step && $nextSubStep instanceof \App\Models\SubStep) {
        $nextUrl = route('cooperation.frontend.tool.quick-scan.index', ['step' => $nextStepKey, 'subStep' => $nextSubStepKey]);
    } else {
        $nextUrl = route('cooperation.frontend.tool.quick-scan.my-plan.index');
    }
    ?>
    @if($previousStep instanceof \App\Models\Step || $previousSubStep instanceof \App\Models\SubStep)
        <a href="{{$previousUrl}}" class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('cooperation/frontend/shared.defaults.previous')
        </a>
    @endif

    <button type="button" wire:click="$emitTo('cooperation.frontend.tool.quick-scan.form', 'save', '{{$nextUrl}}')"  class="btn btn-purple flex items-center ml-1">
        @lang('cooperation/frontend/shared.defaults.next')
        <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
    </button>
</div>
