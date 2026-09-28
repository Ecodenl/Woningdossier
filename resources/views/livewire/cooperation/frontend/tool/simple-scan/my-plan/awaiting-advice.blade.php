<div class="w-full flex justify-center" wire:poll.10s="checkForAdvice">
    <div class="tile max-w-2xl text-center">
        <h3 class="tile-heading">
            @lang('cooperation/frontend/tool.my-plan.label')
        </h3>

        <div class="flex flex-col items-center gap-4 py-6">
            <i class="icon-xl icon-ventilation-fan animate-spin-slow"></i>

            <div class="as-text">
                {!! __('cooperation/frontend/tool.my-plan.awaiting-advice.body') !!}
            </div>
        </div>
    </div>
</div>
