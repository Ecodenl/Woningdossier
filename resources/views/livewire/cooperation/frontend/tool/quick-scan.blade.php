<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
    <div class="w-full">
        @include('cooperation.sub-step-templates.'.$subStep->subStepTemplate->view.'.show');
    </div>
</div>
@include('cooperation.frontend.layouts.parts.step-buttons', [
    'current' => $current,
    'total' => $total,
])


