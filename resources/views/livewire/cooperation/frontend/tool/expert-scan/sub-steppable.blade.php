<div wire:init="init()">
    <div class="grid grid-cols-6 gap-x-6 gap-y-2">
        @foreach($this->subSteppables as $subSteppablePivot)
            @switch($subSteppablePivot->sub_steppable_type)
                @case(\App\Models\ToolQuestion::class)
                    @include('cooperation.frontend.tool.expert-scan.parts.tool-question')
                    @break
                @case(\App\Models\ToolLabel::class)
                    @include('cooperation.frontend.tool.expert-scan.parts.tool-label')
                    @break
                @case(App\Models\ToolCalculationResult::class)
                    @include('cooperation.frontend.tool.expert-scan.parts.tool-calculation-result')
                    @break
            @endswitch
        @endforeach
    </div>
</div>