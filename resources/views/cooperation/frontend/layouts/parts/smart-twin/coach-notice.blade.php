{{-- $show, $scan and $canHandOff come from the SmartTwinCoachNoticeComposer, bound to this view in the ViewServiceProvider. --}}
@if($show)
    <div class="w-full px-5 xl:px-20">
        @component('cooperation.layouts.components.alert', ['color' => 'blue', 'withBackground' => true, 'dismissible' => false])
            <p>@lang('cooperation/frontend/tool.my-plan.coach-notice.text')</p>
            <div class="flex flex-row flex-wrap gap-2 my-3">
                <a href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.house-data', compact('cooperation', 'scan')) }}"
                   class="btn btn-outline-green">
                    @lang('cooperation/frontend/tool.my-plan.house-data.button')
                </a>
                @if($canHandOff)
                    @include('cooperation.frontend.tool.simple-scan.my-plan.parts.adjust-package', [
                        'id' => 'smarttwin-coach-adjust',
                        'label' => __('cooperation/frontend/tool.my-plan.coach-notice.adjust'),
                    ])
                @endif
            </div>
            <p class="font-bold">@lang('cooperation/frontend/tool.my-plan.coach-notice.warning')</p>
        @endcomponent
    </div>
@endif
