<div>
    <div class="grid grid-cols-6 gap-6">
        @foreach($subStep->subSteppables as $subSteppable)
            @switch($subSteppable->sub_steppable_type)
                @case(\App\Models\ToolQuestion::class)
                    @include('cooperation.frontend.tool.expert-scan.parts.tool-question')
                    @break
                @case(\App\Models\ToolLabel::class)

                    @include('cooperation.frontend.tool.expert-scan.parts.tool-label')
                    @break
            @endswitch
        @endforeach
    </div>
</div>