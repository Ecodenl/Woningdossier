<div>
    <div class="w-full divide-blue-500 divide-opacity-20 space-y-5">
        @foreach($subSteppables as $subSteppable)
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