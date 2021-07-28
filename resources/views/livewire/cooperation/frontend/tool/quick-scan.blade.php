<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
    <div class="w-full">
        @include('cooperation.sub-step-templates.'.$subStep->subStepTemplate->view.'.show');
    </div>
</div>

<div class="w-full flex flex-wrap items-center">
    <div class="w-1/4 flex flex-wrap justify-start"></div>
    <div class="w-1/2 flex flex-wrap justify-center">
        <?php
            $previousStepKey = optional($previouStepSlug)->getRouteKey();
            $nextStepKey = optional($nextStepSlug)->getRouteKey();

            $previousSubStepKey = optional($previousSubStep)->getRouteKey();
            $nextSubStepKey = optional($nextSubStep)->getRouteKey();
        ?>
        @if($previousStep instanceof \App\Models\Step)

        @endif
        <a href="{{url("quick-scan/{$previousStep->slug}/{$subStep->previous()->id}")}}" class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('cooperation/frontend/shared.defaults.previous')
        </a>
        <a href="{{url("quick-scan/{$step->nextQuickScan()->slug}/{$subStep->next()->id}")}}" class="btn btn-purple flex items-center ml-1">
            @lang('cooperation/frontend/shared.defaults.next')
            <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
        </a>
    </div>
    <div class="w-1/4 flex flex-wrap justify-end">
        <p>
            {!! __('cooperation/frontend/tool.step-count', ['current' => '<span class="font-bold">' . $current .'</span>', 'total' => $total]) !!}
        </p>
    </div>
</div>



