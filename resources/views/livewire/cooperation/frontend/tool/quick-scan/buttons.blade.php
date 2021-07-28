
<div class="w-1/2 flex flex-wrap justify-center">
<?php
    $previousUrl = null;
    $nextUrl = null;

    $previousStepKey = optional($previousStep)->getRouteKey();
    $nextStepKey = optional($nextStep)->getRouteKey();

    $previousSubStepKey = optional($previousSubStep)->slug;
    $nextSubStepKey = optional($nextSubStep)->slug;

    if ($previousStep instanceof \App\Models\Step && $previousSubStep instanceof \App\Models\SubStep) {
        $previousUrl = url("quick-scan/{$previousStepKey}/$previousSubStepKey");
    }
    if ($nextStep instanceof \App\Models\Step && $nextSubStep instanceof \App\Models\SubStep) {
        $nextUrl = url("quick-scan/{$nextStepKey}/$nextSubStepKey");
    }
    ?>
    <a href="{{$previousUrl}}" class="btn btn-outline-purple
@if($previousStep instanceof \App\Models\Step || $previousSubStep instanceof \App\Models\SubStep)
            disabled
@endif
            flex items-center mr-1">
        <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
        @lang('cooperation/frontend/shared.defaults.previous')
    </a>
    <button wire:click="$emit('save')" class="btn btn-purple flex items-center ml-1">
        @lang('cooperation/frontend/shared.defaults.next')
        <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
    </button>



    <script>
        document.addEventListener('livewire:load', () => {
            window.livewire.on('save', e => {
                Turbolinks.visit("{{$nextUrl}}", { action: "replace" })
            })
        });
    </script>


</div>
